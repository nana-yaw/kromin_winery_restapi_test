<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

            $response = [];

            foreach ($request->file('image') as $image) {
                if ($image->isValid()) {
                    $extension = $image->extension();
                    $name = Utils::genUuid();
                    $wine_id = $request->wine_id;

                    $image->storeAs('/public', $name . '.' . $extension);

                    $url = Storage::url($name . '.' . $extension);
                    $photo = Photo::create(['name' => $name, 'url' => $url, 'wine_id' => $wine_id, 'extension' => $extension]);

                    Session::flash('Uploaded', 'Photo successfully uploaded');
                    array_push($response, $photo);
                } else {
                    return response()->json(['message' => 'Image is not valid'], 400);
                }
            }

            return response()->json($response);
        } else {
            return response()->json(['message' => 'Image is not valid'], 400);
        }
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

        if (isset($request->wine_id)) {
            $db_wine_uuid = DB::table('wines')->where('id', '=', $request->wine_id)->first();
            $rul = ['required_with:wine_id', 'string', 'exists:wines,code', Rule::in([$db_wine_uuid->code])];
        } else {
            $rul = ['required_with:wine_id', 'string', 'exists:wines,code'];
            $validator->sometimes(
                'wine_id', ['required_with:uuid'], function () {
                    return Auth::user()->role()->role != 'admin';
                }
            );
        }

        $validator->sometimes(
            'uuid', $rul, function () {
                return Auth::user()->role()->role != 'admin';
            }
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->limit) {
            $limit = $request->limit;
        } else {
            $limit = env('limit');
        }

        $wine_id = $request->wine_id;

        $uuid = $request->uuid;

        $name = $request->name;

        $from = $request->from;
        $to = $request->to . 'T23:59:59';

        $photos = Photo::query();

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
            $db_wine_id = DB::table('wines')->where('code', '=', $uuid)->first();
            $photos = $photos->where('wine_id', '=', $db_wine_id->id);
            if (!$photos->first()) {
                return response()->json(['message' => 'this wine do not have photo yet'], 400);
            }
        }

        $photos = $photos->paginate($limit);

        return response()->json($photos);
    }

    public function destroy(Photo $photo)
    {
        $path = $photo->name . '.' . $photo->extension;
        Storage::disk('public')->delete($path);
        $photo->delete();

        return response()->json(['message' => 'Photo successfully deleted'], 204);
    }
}
