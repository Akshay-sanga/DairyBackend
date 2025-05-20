<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MilkRate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MilkRatesImport;
use Illuminate\Support\Facades\Validator;
use Smalot\PdfParser\Parser;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Exports\MilkRatesExport;
use Barryvdh\DomPDF\Facade\Pdf;



class MilkRateController extends Controller
{
    // public function index()
    // {
    //     try {

    //         return response()->json(MilkRate::all());
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "status_code" => "500",
    //             "message" => "Something Went Wrong!"
    //         ]);
    //     }
    // }

    public function index()
    {
        $adminId=auth()->user()->id;
        $rates = MilkRate::where('admin_id',$adminId)->get();

        $transformedRates = $rates->map(function ($item) {
            $data = [
                'fat' => $item->fat,
            ];

            foreach ($item->getAttributes() as $key => $value) {
                if (str_starts_with($key, 'snf_')) {
                    $data[$key] = $value;
                }
            }

            return $data;
        });

        return response()->json([
            "status_code" => "200",
            'data' => $transformedRates,
        ]);
    }


    // public function store(Request $request)
    // {
    //     $allRateData = $request->input('rateData');

    //     if (!is_array($allRateData)) {
    //         return response()->json([
    //             'status' => 422,
    //             'message' => 'Invalid data format.'
    //         ]);
    //     }

    //     // Delete old data before inserting new
    //     MilkRate::truncate();
    //      $adminId=auth()->user()->id;
    //     foreach ($allRateData as $item) {

    //         $fat = $item['fat'] ?? null;
    //         $snfRates = collect($item['snfRates'] ?? []);

    //         if (!$fat || !is_numeric($fat)) {
    //             return response()->json([
    //                 'status' => 422,
    //                 'message' => 'The fat field is required.'
    //             ]);
    //         }

    //         $data = ['fat' => $fat];

    //         foreach ($snfRates as $snfRate) {
    //             $snf = number_format((float)($snfRate['snf'] ?? 0), 1);
    //             $key = 'snf_' . str_replace('.', '_', $snf);
    //             $rate = $snfRate['rate'] ?? null;

    //             $data[$key] = is_numeric(trim($rate)) ? (float)trim($rate) : null;
    //         }

    //         MilkRate::create($data);
    //     }

    //     return response()->json([
    //         "status_code" => "200",
    //         'message' => 'All rates saved successfully.',
    //         'data' => MilkRate::all()  // <-- Send full table
    //     ], 201);
    // }



public function store(Request $request)
{
    $allRateData = $request->input('rateData');

    if (!is_array($allRateData)) {
        return response()->json([
            'status' => 422,
            'message' => 'Invalid data format.'
        ]);
    }

    $adminId = auth()->user()->id;

    // ❗ Check if data exists for this admin, then delete old
    $existingData = MilkRate::where('admin_id', $adminId)->exists();
    if ($existingData) {
        MilkRate::where('admin_id', $adminId)->delete();
    }

    foreach ($allRateData as $item) {
        $fat = $item['fat'] ?? null;
        $snfRates = collect($item['snfRates'] ?? []);

        if (!$fat || !is_numeric($fat)) {
            return response()->json([
                'status' => 422,
                'message' => 'The fat field is required.'
            ]);
        }

        $data = [
            'admin_id' => $adminId,
            'fat' => $fat
        ];

        foreach ($snfRates as $snfRate) {
            $snf = number_format((float)($snfRate['snf'] ?? 0), 1);
            $key = 'snf_' . str_replace('.', '_', $snf);
            $rate = $snfRate['rate'] ?? null;

            $data[$key] = is_numeric(trim($rate)) ? (float)trim($rate) : null;
        }

        MilkRate::create($data);
    }

    return response()->json([
        "status_code" => 200,
        'message' => 'Rates saved successfully.',
        'data' => MilkRate::where('admin_id', $adminId)->get()
    ], 201);
}




  public function show($id)
{
    try {
        $adminId = auth()->user()->id;
        $milkRate = MilkRate::where('id', $id)
                            ->where('admin_id', $adminId)
                            ->firstOrFail();

        return response()->json([
            "status_code" => 200,
            "data" => $milkRate
        ]);

    } catch (\Exception $e) {
        return response()->json([
            "status_code" => 500,
            "message" => "Something Went Wrong or Record Not Found!"
        ]);
    }
}

    public function update(Request $request, $id)
    {
        try {
            $milkRate = MilkRate::findOrFail($id);
            $milkRate->update($request->all());
            return response()->json(
                [
                    "status_code" => "200",
                    $milkRate
                ]
                );
        } catch (\Exception $e) {
            return response()->json([
                "status_code" => "500",
                "message" => "Something Went Wrong!"
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $milkRate = MilkRate::findOrFail($id);
            $milkRate->delete();
            return response()->json([
                 "status_code" => "200",
                'message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                "status_code" => "500",
                "message" => "Something Went Wrong!"
            ]);
        }
    }





    // public function importFile(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|mimes:xlsx,xls,csv,pdf|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 400);
    //     }

    //     $file = $request->file('file');
    //     $extension = $file->getClientOriginalExtension();
    //     $adminId = auth()->user()->id;

    //     MilkRate::truncate();

    //     if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
    //         // Excel file import
    //         Excel::import(new MilkRatesImport, $file);
    //         return response()->json(['message' => 'Excel file imported successfully']);
    //     } elseif ($extension === 'pdf') {
    //         // PDF file import
    //         $parser = new Parser();
    //         $pdf = $parser->parseFile($file->getPathname());
    //         $text = $pdf->getText();

    //         $lines = explode("\n", $text);

    //         foreach ($lines as $index => $line) {
    //             if ($index === 0) continue; // Skip the header row

    //             $columns = preg_split('/\s+/', trim($line));

    //             if (count($columns) >= 9) {
    //                 MilkRate::create([
    //                     'admin_id'      => $adminId,
    //                     'fat'      => $columns[0],
    //                     'snf_8_3'  => $columns[1],
    //                     'snf_8_4'  => $columns[2],
    //                     'snf_8_5'  => $columns[3],
    //                     'snf_8_6'  => $columns[4],
    //                     'snf_8_7'  => $columns[5],
    //                     'snf_8_8'  => $columns[6],
    //                     'snf_8_9'  => $columns[7],
    //                     'snf_9_0'  => $columns[8],
    //                 ]);
    //             }
    //         }
    //         return response()->json(['message' => 'PDF file imported successfully']);
    //     } else {
    //         return response()->json(['error' => 'Unsupported file type'], 400);
    //     }
    // }

public function importFile(Request $request)
{
    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:xlsx,xls,csv,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $file = $request->file('file');
    $extension = $file->getClientOriginalExtension();
    $adminId = auth()->user()->id;

    if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
        // Excel file import
        Excel::import(new MilkRatesImport($adminId), $file);
        return response()->json(['message' => 'Excel file imported successfully']);
    } elseif ($extension === 'pdf') {
        // PDF file import
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $text = $pdf->getText();

        $lines = explode("\n", $text);

        foreach ($lines as $index => $line) {
            if ($index === 0) continue; // Skip the header row

            $columns = preg_split('/\s+/', trim($line));

            if (count($columns) >= 9) {
                // Check if record exists for admin and fat
                $existing = MilkRate::where('admin_id', $adminId)
                                    ->where('fat', $columns[0])
                                    ->first();

                $data = [
                    'admin_id' => $adminId,
                    'fat' => $columns[0],
                    'snf_8_3' => $columns[1],
                    'snf_8_4' => $columns[2],
                    'snf_8_5' => $columns[3],
                    'snf_8_6' => $columns[4],
                    'snf_8_7' => $columns[5],
                    'snf_8_8' => $columns[6],
                    'snf_8_9' => $columns[7],
                    'snf_9_0' => $columns[8],
                ];

                if ($existing) {
                    $existing->update($data);
                } else {
                    MilkRate::create($data);
                }
            }
        }

        return response()->json(['message' => 'PDF file imported successfully']);
    } else {
        return response()->json(['error' => 'Unsupported file type'], 400);
    }
}


    // public function exportDemoBoth()
    // {
    //     set_time_limit(300); // Increase execution time

    //     $demoData = [
    //         ['fat' => '4.0', 'snf_8_3' => '30', 'snf_8_4' => '31', 'snf_8_5' => '32', 'snf_8_6' => '33', 'snf_8_7' => '34', 'snf_8_8' => '35', 'snf_8_9' => '36', 'snf_9_0' => '37'],
    //         ['fat' => '4.5', 'snf_8_3' => '32', 'snf_8_4' => '33', 'snf_8_5' => '34', 'snf_8_6' => '35', 'snf_8_7' => '36', 'snf_8_8' => '37', 'snf_8_9' => '38', 'snf_9_0' => '39'],
    //     ];

    //     $timestamp = now()->format('Ymd_His');
    //     $zipFileName = "milk_rate_demo_{$timestamp}.zip";

    //     $tempPath = storage_path('app/temp_export');
    //     File::ensureDirectoryExists($tempPath);

    //     // Excel
    //     $excelPath = $tempPath . '/milk_rate_demo.xlsx';
    //     Excel::store(new MilkRatesExport($demoData), 'temp_export/milk_rate_demo.xlsx');


    //     $html = '<h1>Milk Rate Demo</h1><table border="1" cellpadding="5">';
    //     $html .= '<tr><th>Fat</th><th>SNF 8.3</th><th>SNF 8.4</th><th>SNF 8.5</th><th>SNF 8.6</th><th>SNF 8.7</th><th>SNF 8.8</th><th>SNF 8.9</th><th>SNF 9.0</th></tr>';

    //     foreach ($demoData as $row) {
    //         $html .= '<tr>';
    //         foreach ($row as $value) {
    //             $html .= "<td>{$value}</td>";
    //         }
    //         $html .= '</tr>';
    //     }
    //     $html .= '</table>';

    //     $pdf = Pdf::loadHTML($html);
    //     $pdfPath = $tempPath . '/milk_rate_demo.pdf';
    //     $pdf->save($pdfPath);

    //     // ZIP
    //     $zip = new ZipArchive;
    //     $zipPath = $tempPath . '/' . $zipFileName;
    //     if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
    //         $zip->addFile($excelPath, 'milk_rate_demo.xlsx');
    //         $zip->addFile($pdfPath, 'milk_rate_demo.pdf');
    //         $zip->close();
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Milk rate demo files generated successfully.',
    //         'zip_path' => asset('storage/temp_export/' . $zipFileName)
    //     ]);
    // }




public function exportDemoBoth()
{
    set_time_limit(300); // Increase execution time

    $adminId = auth()->user()->id; // Get logged-in admin ID

    // Fetch only that admin's data
    $milkRates = MilkRate::where('admin_id', $adminId)->get([
        'fat', 'snf_8_3', 'snf_8_4', 'snf_8_5', 'snf_8_6',
        'snf_8_7', 'snf_8_8', 'snf_8_9', 'snf_9_0'
    ]);

    $demoData = $milkRates->toArray(); // Convert to array

    if (empty($demoData)) {
        return response()->json([
            'success' => false,
            'message' => 'No milk rate data found for the logged-in admin.',
        ]);
    }

    $timestamp = now()->format('Ymd_His');
    $zipFileName = "milk_rate_demo_{$timestamp}.zip";

    $tempPath = storage_path('app/temp_export');
    File::ensureDirectoryExists($tempPath);

    // Excel
    $excelPath = $tempPath . '/milk_rate_demo.xlsx';
    Excel::store(new MilkRatesExport($demoData), 'temp_export/milk_rate_demo.xlsx');

    // Create PDF HTML
    $html = '<h1>Milk Rate Demo</h1><table border="1" cellpadding="5">';
    $html .= '<tr><th>Fat</th><th>SNF 8.3</th><th>SNF 8.4</th><th>SNF 8.5</th><th>SNF 8.6</th><th>SNF 8.7</th><th>SNF 8.8</th><th>SNF 8.9</th><th>SNF 9.0</th></tr>';

    foreach ($demoData as $row) {
        $html .= '<tr>';
        foreach ($row as $value) {
            $html .= "<td>{$value}</td>";
        }
        $html .= '</tr>';
    }
    $html .= '</table>';

    $pdf = Pdf::loadHTML($html);
    $pdfPath = $tempPath . '/milk_rate_demo.pdf';
    $pdf->save($pdfPath);

    // Create ZIP
    $zip = new ZipArchive;
    $zipPath = $tempPath . '/' . $zipFileName;
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($excelPath, 'milk_rate_demo.xlsx');
        $zip->addFile($pdfPath, 'milk_rate_demo.pdf');
        $zip->close();
    }

    return response()->json([
        'success' => true,
        'message' => 'Milk rate demo files generated successfully.',
        'zip_path' => asset('storage/temp_export/' . $zipFileName)
    ]);
}




}