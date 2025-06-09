<?php

namespace App\Http\Controllers;

use App\Models\ProductSale;
use App\Models\Customer;
use App\Models\ProductMaster;
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
        $adminId = auth()->user()->id;

        $productId = $request->product_id;
        $qty = $request->qty;

        $productStock = \DB::table('product_stocks')
            ->where('product_id', $productId)
            ->where('admin_id', $adminId)
            ->orderByDesc('id') 
            ->first();

        if ($productStock) {
            $newQuantity = $productStock->quantity - $qty;

            if ($newQuantity < 0) {
                return response([
                    "status_code" => 400,
                    "message" => "Insufficient stock for product ID: $productId"
                ]);
            }

            \DB::table('product_stocks')
                ->where('id', $productStock->id)
                ->update([
                    'quantity' => $newQuantity,
                    'updated_at' => now()
                ]);
        }

        
        \DB::table('product_sales')->insert([
            'admin_id' => $adminId,
            'customer_account_number' => $request->customer_account_number,
            'date' => $request->date,
            'category_id' => $request->category_id,
            'product_id' => $productId,
            'product_price' => $request->product_price,
            'qty' => $qty,
            'total' => $request->total,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

       
        $customer = Customer::where('account_number', $request->customer_account_number)->first();
        if ($customer) {
            $customer->wallet -= $request->total;
            $customer->save();
        }
      $productSales = ProductSale::with('customer')->get();

        return response([
            "status_code" => 200,
            "message" => "Product Sale Recorded Successfully",
            "data"=>$productSales,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}


    
    
    
    
    
    
    

// public function submit(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         "customer_account_number" => "required",
//         "date" => "required",
//         "category_id" => "required|array",
//         "product_id" => "required|array",
//         "product_price" => "required|array",
//         "qty" => "required|array",
//         "total" => "required|array",
//     ]);

//     if ($validator->fails()) {
//         return response([
//             "status_code" => 422,
//             "message" => $validator->errors()->first()
//         ]);
//     }

//     try {
//         $adminId = auth()->user()->id;

//         $categoryIds = $request->category_id;
//         $productIds = $request->product_id;
//         $productPrices = $request->product_price;
//         $qtys = $request->qty;
//         $totals = $request->total;

//         $insertData = [];
//         $grandTotal = 0;

//         for ($i = 0; $i < count($productIds); $i++) {
//             $productId = $productIds[$i];
//             $qty = $qtys[$i];

//             // Reduce stock quantity for the given product
//             $productStock = \DB::table('product_stocks')
//                 ->where('product_id', $productId)
//                 ->where('admin_id', $adminId)
//                 ->orderByDesc('id') // Use latest stock entry
//                 ->first();

//             if ($productStock) {
//                 $newQuantity = $productStock->quantity - $qty;

//                 // Ensure stock doesn't go negative
//                 if ($newQuantity < 0) {
//                     return response([
//                         "status_code" => 400,
//                         "message" => "Insufficient stock for product ID: $productId"
//                     ]);
//                 }

//                 // Update the stock
//                 \DB::table('product_stocks')
//                     ->where('id', $productStock->id)
//                     ->update([
//                         'quantity' => $newQuantity,
//                         'updated_at' => now()
//                     ]);
//             }

//             // Prepare data for sale insert
//             $insertData[] = [
//                 'admin_id' => $adminId,
//                 'customer_account_number' => $request->customer_account_number,
//                 'date' => $request->date,
//                 'category_id' => $categoryIds[$i],
//                 'product_id' => $productId,
//                 'product_price' => $productPrices[$i],
//                 'qty' => $qty,
//                 'total' => $totals[$i],
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ];

//             $grandTotal += $totals[$i];
//         }

//         // Insert sales data
//         \DB::table('product_sales')->insert($insertData);

//         // Update customer wallet
//         $customer = Customer::where('account_number', $request->customer_account_number)->first();
//         if ($customer) {
//             $customer->wallet -= $grandTotal;
//             $customer->save();
//         }

//         return response([
//             "status_code" => 200,
//             "message" => "Product Sale Recorded Successfully",
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status_code' => 500,
//             'message' => 'Something went wrong.',
//             'error' => $e->getMessage()
//         ]);
//     }
// }


public function all()
{
    try {
        $sales = ProductSale::with(['customer:id,name', 'category:id,name', 'product:id,name'])
            ->orderBy('id', 'desc')
            ->paginate(10); // Pagination should be before ->get()

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
              $customerAccountNumber = $request->customer_account_number;
        $customer = Customer::where('account_number', $customerAccountNumber)->first();
        if ($customer) {
            $customer->wallet -= $request->total_amount;
            $customer->save();
        }

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


public function fetchProduct(Request $request)
{
    try{
        $category_id = $request->category_id;
        $adminId = auth()->user()->id;
        $data = ProductMaster::where('admin_id',$adminId)->where('category_id',$category_id)->where('status','1')->get();
         return response([
            'status_code' => 200,
            'data'=>$data,
            'message' => 'Product Fetch successfully.'
        ]);
        
    }
     catch (\Exception $e) {
        return response([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}




}
