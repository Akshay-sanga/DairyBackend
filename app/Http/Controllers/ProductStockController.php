<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ProductStockController extends Controller
{
    public function submit(Request $request)
{
    $validate = Validator::make($request->all(), [
        'product_id' => 'required',
        'stock_type' => 'required|in:IN,OUT',
        'quantity' => 'required|numeric|min:1',
        'date' => 'required|date',
    ]);

    if ($validate->fails()) {
        return response()->json([
            'status_code' => 400,
            'response' => 'error',
            'data' => new \stdClass(),
            'message' => $validate->messages()->first(),
        ]);
    }

    try {
        // Check if the product stock already exists
        $existingStock = ProductStock::where('product_id', $request->product_id)->first();

        if ($existingStock) {
            // Update existing stock
            if ($request->stock_type === 'IN') {
                $existingStock->quantity += $request->quantity;
            } elseif ($request->stock_type === 'OUT') {
                if ($existingStock->quantity < $request->quantity) {
                    return response()->json([
                        'status_code' => 400,
                        'response' => 'error',
                        'message' => 'Not enough stock to subtract.',
                    ]);
                }
                $existingStock->quantity -= $request->quantity;
            }

            $existingStock->date = $request->date;
            $existingStock->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Product Stock Updated Successfully',
                'response' => 'success',
            ]);
        } else {
            // Create new stock entry
            $model = new ProductStock();
            $model->product_id = $request->product_id;
            $model->stock_type = $request->stock_type;
            $model->quantity = $request->quantity;
            $model->date = $request->date;
            $model->status = '1';
            $model->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Product Stock Added Successfully',
                'response' => 'success',
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'response' => 'error',
            'message' => 'Something Went Wrong',
        ]);
    }
}

    public function all()
    {
        try{
            $data = ProductStock::with('product')->orderBy('id', 'desc')->get();
            return response()->json([
                'status_code'=>'200',
                'response'=>'success',
                'data'=>$data
            ]);
        } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'response' => 'error',
            'message' => 'Something Went Wrong',
        ]);
    }
    }

    public function edit(Request $request,$id)
    {
        try{
            $data = ProductStock::where('id',$id)->get();
            return response()->json([
                'status_code'=>'200',
                'response'=>'success',
                'data'=>$data
            ]);
        }
         catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'response' => 'error',
            'message' => 'Something Went Wrong',
        ]);
    }
}


public function update(Request $request, $id)
{
    $validate = Validator::make($request->all(), [
        'product_id' => 'required',
        'stock_type' => 'required|in:IN,OUT',
        'quantity' => 'required|numeric|min:1',
        'date' => 'required|date',
    ]);

    if ($validate->fails()) {
        return response()->json([
            'status_code' => 400,
            'response' => 'error',
            'data' => new \stdClass(),
            'message' => $validate->messages()->first(),
        ]);
    }

    try {
        // Find the existing stock record by ID
        $stock = ProductStock::find($id);

        if (!$stock) {
            return response()->json([
                'status_code' => 404,
                'response' => 'error',
                'message' => 'Product Stock Not Found',
            ]);
        }

        // Update logic based on stock_type
        if ($request->stock_type === 'IN') {
            $stock->quantity += $request->quantity;
        } elseif ($request->stock_type === 'OUT') {
            if ($stock->quantity < $request->quantity) {
                return response()->json([
                    'status_code' => 400,
                    'response' => 'error',
                    'message' => 'Not enough stock to subtract.',
                ]);
            }
            $stock->quantity -= $request->quantity;
        }

        // Update remaining fields
        $stock->stock_type = $request->stock_type;
        $stock->date = $request->date;
        $stock->product_id = $request->product_id;
        $stock->save();

        return response()->json([
            'status_code' => 200,
            'response' => 'success',
            'message' => 'Product Stock Updated Successfully',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'response' => 'error',
            'message' => 'Something Went Wrong',
        ]);
    }
}


public function delete(Request $request,$id)
{
    try{
        ProductStock::where('id',$id)->delete();
        return response()->json([
            'status_code' =>'200',
            'response'=>'success',
            'message'=>'Product Stock Deleted'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'response' => 'error',
            'message' => 'Something Went Wrong',
        ]);
    }

}



public function UpdateStatus(Request $request, $id)
   {
       try {
           $product = ProductStock::find($id);
   
           if (!$product) {
               return response([
                   "status_code" => 404,
                   "message" => "product stock not found.",
               ]);
           }
   
           // Toggle status
           $product->status = $product->status == '1' ? '0' : '1';
           $product->save();
   
           return response([
               "status_code" => 200,
               "message" => "product stock status updated successfully.",
               "status" => $product->status, 
           ]);
   
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something went wrong.',
           ]);
       }
   }





}
