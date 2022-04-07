<?php

namespace App\Http\Controllers;

use App\utils;
use App\Models\Wine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use App\Http\Resources\WineResource;
use Illuminate\Support\Facades\Validator;

class WineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
            'limit' => ['integer'],
            'col' => ['string', Rule::in(['created_at', 'name', 'description']), 'required_with:col'],
            'sort' => ['string', Rule::in(['asc', 'desc']), 'required_with:col'],
            'from' => ['date', 'required_with:to'],
            'to' => ['date', 'required_with:from'],
            'from_year' => ['integer', 'required_with:to_year'],
            'to_year' => ['integer', 'required_with:from_year'],
            'name_like' => ['string'],
            'colour' => ['string', Rule::in(['bianco', 'rosso', 'rosato'])],
            'effervescence' => ['string', Rule::in(['fermo', 'frizzante', 'spumante'])],
            ]
        );

        $rule = $this->getEffervescenceValidateRule($request->effervescence);

        $validator->sometimes(
            'sweetness', $rule, function ($request) {
                return $request->effervescence;
            }
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $limit = ($request->limit) ? $request->limit : env('limit');

        $col = $request->col;
        $sort = $request->sort;

        $from = $request->from;
        $to = $request->to.'T23:59:59';

        $from_year = $request->from_year;
        $to_year = $request->to_year;

        $name_like = $request->name_like;

        $colour = $request->colour;
        $effervescence = $request->effervescence;
        $sweetness = $request->sweetness;

        $wines=  Wine::query()->with('photos');

        if($from) {
            $wines = $wines->whereBetween('created_at', [$from, $to]);
        }

        if($from_year) {
            $wines = $wines->whereBetween('year', [$from_year, $to_year]);
        }

        if($name_like) {
            $wines = $wines->where('name', 'like', '%'.$name_like.'%');
        }

        if($col) {
            $wines = $wines->orderBy($col, $sort);
        }

        if($colour) {
            $wines = $wines->where('colour', '=', $colour);
        }

        if($effervescence) {
            $wines = $wines->where('effervescence', '=', $effervescence);
        }

        if($sweetness) {
            $wines = $wines->where('sweetness', '=', $sweetness);
        }

        $wines = WineResource::collection($wines->paginate($limit));

        return $wines;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'year' => ['required', 'integer'],
            'colour' => ['required', 'string', Rule::in(['bianco', 'rosso', 'rosato'])],
            'effervescence' => ['required', 'string', Rule::in(['fermo', 'frizzante', 'spumante'])],
            ]
        );

        $rule = $this->getEffervescenceValidateRule($request->effervescence);

        $validator->sometimes(
            'sweetness', $rule, function ($request) {
                return $request->effervescence;
            }
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $wine_attribute = $request->all();
        $wine_attribute['code'] = Utils::genUuid();
        $wine = Wine::create($wine_attribute);

        return response(['wine' => new WineResource($wine), 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Wine $wine
     */
    public function show(Wine $wine)
    {
        if($wine) {
            return response(['wine' => new WineResource($wine->loadMissing(['photos'])), 'message' => 'Retrieved successfully'], 200);
        }
        return response()->json(['message' => 'Wine not found or incorrect wine code.'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Wine         $wine
     */
    public function update(Request $request, Wine $wine)
    {
        $validator = Validator::make(
            $request->all(), [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'year' => ['required', 'integer'],
            'colour' => ['required', 'string', Rule::in(['bianco', 'rosso', 'rosato'])],
            'effervescence' => ['required', 'string', Rule::in(['fermo', 'frizzante', 'spumante'])],
            ]
        );

        $rule = $this->getEffervescenceValidateRule($request->effervescence);

        $validator->sometimes(
            'sweetness', $rule, function ($request) {
                return $request->effervescence;
            }
        );

        if ($validator->fails()) { return response()->json($validator->errors(), 400);
        }

        $wine_attribute = $request->all();
        $wine_attribute['code'] = $wine['code'];
        $wine-> update($wine_attribute);

        return response(['wine' => new WineResource($wine), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Wine $wine
     */
    public function destroy(Wine $wine)
    {

        $wine->delete();

        return response()->json(['message' => 'Wine successfully deleted'], 204);
    }

    private function getEffervescenceValidateRule($effervescence) {
        $validateRule = ($effervescence == "fermo") ? ['required', 'string', Rule::in(['secco', 'abboccato', 'amabile', 'dolce'])] : ['secco', 'abboccato', 'brut', 'dolce', 'extra-brut', 'dosaggio zero', 'extra-dry'];
        return $validateRule;
    }


}
