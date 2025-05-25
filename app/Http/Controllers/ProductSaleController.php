<?php

namespace App\Http\Controllers;

use App\Models\ProductSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductSaleController extends Controller
{

   public function submit(Request $request)
{
    $validator = Validator::make($request->all(), [
        "customer_account_number" => "required",
        "date" => "required",
        "category_id" => "required|array",
        "product_id" => "required|array",
        "product_price" => "required|array",
        "qty" => "required|array",
        "total" => "required|array",
    ]);

    if ($validator->fails()) {
        return response([
            "status_code" => 422,
            "message" => $validator->errors()->first()
        ]);
    }

    try {
        $adminId = auth()->user()->id;

        $categoryIds = $request->category_id;
        $productIds = $request->product_id;
        $productPrices = $request->product_price;
        $qtys = $request->qty;
        $totals = $request->total;

        $insertData = [];

        for ($i = 0; $i < count($categoryIds); $i++) {
            $insertData[] = [
                'admin_id' => $adminId,
                'customer_account_number' => $request->customer_account_number,
                'date' => $request->date,
                'category_id' => $categoryIds[$i],
                'product_id' => $productIds[$i],
                'product_price' => $productPrices[$i],
                'qty' => $qtys[$i],
                'total' => $totals[$i],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \DB::table('product_sales')->insert($insertData);

        return response([
            "status_code" => 200,
            "message" => "Product Sale Recorded Successfully",
            // "data" => $insertData
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}


public function all()
{
    try {
       $sales = ProductSale::with(['customer', 'category', 'product'])
            ->orderBy('id', 'desc')
            ->get();

        return response([
            'status_code' => 200,
            'message' => 'All product sales fetched successfully.',
            'data' => $sales
        ]);
    } catch (\Exception $e) {
        return response([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}

public function edit($id)
{
    try {
        $sale = DB::table('product_sales')->where('id', $id)->first();

        if (!$sale) {
            return response([
                'status_code' => 404,
                'message' => 'Product sale not found.'
            ]);
        }

        return response([
            'status_code' => 200,
            'message' => 'Product sale fetched successfully.',
            'data' => $sale
        ]);
    } catch (\Exception $e) {
        return response([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}


public function delete($id)
{
    try {
        $deleted = DB::table('product_sales')->where('id', $id)->delete();

        if (!$deleted) {
            return response([
                'status_code' => 404,
                'message' => 'Product sale not found.'
            ]);
        }

        return response([
            'status_code' => 200,
            'message' => 'Product sale deleted successfully.'
        ]);
    } catch (\Exception $e) {
        return response([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}

public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        "customer_account_number" => "required",
        "date" => "required",
        "category_id" => "required",
        "product_id" => "required",
        "product_price" => "required",
        "qty" => "required",
        "total" => "required",
    ]);

    if ($validator->fails()) {
        return response([
            "status_code" => 422,
            "message" => $validator->errors()->first()
        ]);
    }

    try {
        $updated = DB::table('product_sales')
            ->where('id', $id)
            ->update([
                'customer_account_number' => $request->customer_account_number,
                'date' => $request->date,
                'category_id' => $request->category_id,
                'product_id' => $request->product_id,
                'product_price' => $request->product_price,
                'qty' => $request->qty,
                'total' => $request->total,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return response([
                'status_code' => 404,
                'message' => 'Product sale not found or no changes made.'
            ]);
        }

        return response([
            'status_code' => 200,
            'message' => 'Product sale updated successfully.'
        ]);
    } catch (\Exception $e) {
        return response([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}




}
