<?php

namespace App\Http\Controllers;

use App\Models\ProductMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ProductMasterController extends Controller
{
    public function submit(Request $request)
    {
         $validate = Validator::make($request->all(), [
        'category_id' => 'required',
        'name' => 'required',
        'unit' => 'required',
        'price' => 'required',
    ]);
  
    if ($validate->fails()) {
        return response()->json([
            'status_code' => 400, // Use 400 for validation errors
            'response' => 'error',
            'data' => new \stdClass(),
            'message' => $validate->messages()->first(),
        ]);
    }
    try{
        $model=new ProductMaster();
        $model->category_id=$request->category_id;
        $model->name=$request->name;
        $model->unit=$request->unit;
        $model->price=$request->price;
        $model->status='1';
        $model->save();
        return response()->json([
            'status_code'=>'200',
            'message'=>'Product Added Successfully',
            'response'=>'success'
        ]);
    }
    catch(\Exception $e){
        return response()->json([
            'status_code'=>'500',
            'response'=>'error',
            'message'=>'Something Went Wrong'
        ]);
        }
    }

    public function all()
    {
        try{
            $data = ProductMaster::with(['category', 'stocks'])->orderBy('id', 'desc')->get();

            return response()->json([
                'status_code'=>'200',
                'response'=>'success',
                'data'=>$data
            ]);
        }
         catch(\Exception $e){
        return response()->json([
            'status_code'=>'500',
            'response'=>'error',
            'message'=>'Something Went Wrong'
        ]);
        }

    }

    public function edit(Request $request,$id)
    {
        try{
            $data = ProductMaster::find($id);
            return response()->json([
                'status_code'=>'200',
                'response'=>'success',
                'data'=>$data
            ]);
        }
            catch(\Exception $e){
        return response()->json([
            'status_code'=>'500',
            'response'=>'error',
            'message'=>'Something Went Wrong'
        ]);
        }
    }


    public function update(Request $request,$id)
    {
           $validate = Validator::make($request->all(), [
        'category_id' => 'required',
        'name' => 'required',
        'unit' => 'required',
        'price' => 'required',
    ]);
  
    if ($validate->fails()) {
        return response()->json([
            'status_code' => 400, // Use 400 for validation errors
            'response' => 'error',
            'data' => new \stdClass(),
            'message' => $validate->messages()->first(),
        ]);
    }

    try{
        $model['category_id']=$request->post('category_id');
        $model['name']=$request->post('name');
        $model['unit']=$request->post('unit');
        $model['price']=$request->post('price');
        ProductMaster::where('id',$id)->update($model);
        return response()->json([
            'status_code'=>'200',
            'response'=>'success',
            'message'=>'Product Updated Successfully'
        ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status_code'=>'500',
                'response'=>'error',
                'message'=>'something Went Wrong'
            ]);
    }



    }


    public function delete(Request $request,$id)
    {
        try{
            ProductMaster::where('id',$id)->delete();
            return response()->json([
                'status_code'=>'200',
                'response'=>'success',
                'message'=>'Product Deleted'
            ]);
        }
         catch(\Exception $e){
            return response()->json([
                'status_code'=>'500',
                'response'=>'error',
                'message'=>'something Went Wrong'
            ]);
    }

    }

public function UpdateStatus(Request $request, $id)
   {
       try {
           $product = ProductMaster::find($id);
   
           if (!$product) {
               return response([
                   "status_code" => 404,
                   "message" => "product not found.",
               ]);
           }
   
           // Toggle status
           $product->status = $product->status == '1' ? '0' : '1';
           $product->save();
   
           return response([
               "status_code" => 200,
               "message" => "product status updated successfully.",
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
