<?php

namespace App\Http\Controllers;

use App\Models\SnfFormula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SnfFormulaController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $formulaArray = $request->input('data');

        // Flatten to single associative array: ['A' => 4, 'B' => 0.2, 'C' => 0.66]
        $flattened = [];
        foreach ($formulaArray as $item) {
            $flattened = array_merge($flattened, $item);
        }

        $validator = Validator::make($flattened, [
            'A' => 'required|numeric',
            'B' => 'required|numeric',
            'C' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'response' => 'error',
                'data' => new \stdClass(),
                'message' => $validator->messages()->first(),
            ]);
        }

        $model = SnfFormula::firstOrNew(); // if only one should exist
        $model->A = $flattened['A'];
        $model->B = $flattened['B'];
        $model->C = $flattened['C'];
        $model->save();

        return response()->json([
            'status_code' => 200,
            'response' => 'success',
            'message' => 'SNF formula saved successfully.',
        ]);
    }

    // Show latest values in formula tab on fronend
    public function getLatest()
    {
        $formula = SnfFormula::latest()->first(); // gets the latest saved formula

        if (!$formula) {
            return response()->json([
                'status_code' => 404,
                'message' => 'No SNF formula found.',
                'data' => new \stdClass()
            ]);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Latest SNF formula retrieved.',
            'data' => [
                'A' => $formula->A,
                'B' => $formula->B,
                'C' => $formula->C,
            ]
        ]);
    }
}