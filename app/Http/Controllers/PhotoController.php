<?php

namespace App\Http\Controllers;

use App\Utils;
use App\Models\Wine;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PhotoResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('image')) {
            $validator = Validator::make(
                $request->all(), [
                    'image' => ['required'],
                    'image.*' => ['mimes:jpeg,png,jpg'],
                    'wine_id' => ['required', 'integer', 'exists:wines,id'],
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $album = [];

            foreach ($request->file('image') as $image) {
                if ($image->isValid()) {
                    $extension = $image->extension();
                    $name = Utils::genUuid();
                    $wine_id = $request->wine_id;

                    $image->storeAs('/public', $name . '.' . $extension);

                    $url = Storage::url($name . '.' . $extension);
                    $photo = Photo::create(['name' => $name, 'url' => $url, 'wine_id' => $wine_id, 'extension' => $extension]);
                    array_push($album, $photo);
                }
                return response()->json(['message' => 'Image invalid'], 400);
            }

            return response(['photo' => new PhotoResource($album), 'message' => 'Created successfully'], 201);
        }
        return response()->json(['message' => 'Image is not found'], 404);
    }

    public function index(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
                'limit' => ['integer'],
                'wine_id' => ['integer', 'exists:wines,id'],
                'uuid' => ['string', 'exists:wines,code'],
                'name' => ['string'],
                'from' => ['date', 'required_with:to'],
                'to' => ['date', 'required_with:from'],
            ]
        );

        $rule = ['required_with:wine_id', 'string', 'exists:wines,code'];
        $wineByCode = Wine::where('code', $request->uuid)->first();
        if (isset($request->wine_id)) {
            $wine = Wine::where('id', $request->wine_id)->first();
            $rule = ['required_with:wine_id', 'string', 'exists:wines,code', Rule::in([$wine->code])];
        }

        $validator->sometimes(
            'wine_id', ['required_with:uuid'], function () {
                return Auth::user()->role()->role != 'admin';
            }
        );

        $validator->sometimes(
            'uuid', $rule, function () {
                return Auth::user()->role()->role != 'admin';
            }
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $limit = ($request->limit) ? $request->limit : env('limit');

        $wine_id = $request->wine_id;

        $uuid = $request->uuid;

        $name = $request->name;

        $from = $request->from;
        $to = $request->to . 'T23:59:59';

        $photos = Photo::query()->with(['wine']);

        if ($from) {
            $photos = $photos->whereBetween('created_at', [$from, $to]);
        }

        if ($name) {
            $photos = $photos->where('name', '=', $name);
        }

        if ($wine_id) {
            $photos = $photos->where('wine_id', '=', $wine_id);
        }

        if ($uuid) {
            $photos = $wineByCode->photos;
            if (!$photos->first()) {
                return response()->json(['message' => 'this wine do not have any photo yet'], 400);
            }
        }

        // $photos = $photos;

        return PhotoResource::collection($photos->paginate($limit));
    }

    public function destroy(Photo $photo)
    {
        $path = $photo->name . '.' . $photo->extension;
        Storage::disk('public')->delete($path);
        $photo->delete();

        return response()->json(['message' => 'Photo successfully deleted'], 204);
    }
}
