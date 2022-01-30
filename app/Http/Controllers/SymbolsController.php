<?php

namespace App\Http\Controllers;

use App\Domain\Symbols\Models\Symbol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SymbolsController extends Controller
{
    public function show(Request $request) : JsonResponse
    {
        $symbols = $request->get('data');
        if (!$symbols) {
            return response()->json([]);
        }

        $symbolsValue = array_map(function ($symbol) {
            $term = '{' . $symbol . '}';
            $search = Symbol::where('symbol', '=', $term)->first();

            return [
                'svg'        => $search->svgPath ? Storage::url($search->svgPath) : '',
                'symbolText' => $term,
            ];
        }, $symbols);

        return response()->json($symbolsValue);
    }
}
