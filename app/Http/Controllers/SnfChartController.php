<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\SnfChart;

class SnfChartController extends Controller
{
public function store(Request $request)
{
  

    $request->validate([
        'data' => 'required|array',
    ]);

    $data = $request->input('data');

    if (!is_array($data)) {
        return response()->json(['error' => 'Invalid data format. "data" must be an array.'], 400);
    }

    foreach ($data as $row) {
        SnfChart::updateOrCreate(
            ['fat' => $row['fat']],
            [
                'clr_22' => $row['clr_22'],
                'clr_23' => $row['clr_23'],
                'clr_24' => $row['clr_24'],
                'clr_25' => $row['clr_25'],
                'clr_26' => $row['clr_26'],
                'clr_27' => $row['clr_27'],
                'clr_28' => $row['clr_28'],
                'clr_29' => $row['clr_29'],
                'clr_30' => $row['clr_30'],
            ]
        );
    }

    return response()->json(['message' => 'SNF Chart updated successfully.']);
}

}
