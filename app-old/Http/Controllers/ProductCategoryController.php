<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Paginate;

class ProductCategoryController extends Controller
{
   public function submit(Request $request)
   {
    $validate = Validator::make($request->all(), [
        'name' => 'required',
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
        $adminId=auth()->user()->id;
        $model = new ProductCategory();
        $model->admin_id=$adminId;
        $model->name=$request->name;
        $model->status='1';
        $model->save();
        return response()->
        json([
            'status_code' =>200,
            'response' =>'success',
        ]);
    }
    catch(\Exception $e){
        return response()->json([
            'status_code' =>'500',
            'message'=>'Something Went Wrong'
        ]);
        }
   }

   public function all()
   {
    try{
        $adminId=auth()->user()->id;
        $data = ProductCategory::where('admin_id',$adminId)->orderBy('id','desc')->paginate(10);
        return response()->json([
            'status_code' =>'200',
            'response' =>'success',
            'data'=>$data,
            ]);
    }
      catch(\Exception $e){
        return response()->json([
            'status_code' =>'500',
            'message'=>'Something Went Wrong'
        ]);
        }

   }

    public function edit(Request $request,$id)
    {
        try{
            $data=ProductCategory::where('id',$id)->get();
            return response()->json([
                'status_code'=>'200',
                'response'=>'success',
                'data'=>$data
            ]);
        }
         catch(\Exception $e){
        return response()->json([
            'status_code' =>'500',
            'message'=>'Something Went Wrong'
        ]);
        }
    }


    public function update(Request $request,$id)
    {
         $validate = Validator::make($request->all(), [
        'name' => 'required',
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
        $model['name']=$request->post('name');
        ProductCategory::where('id',$id)->update($model);
        return response()->json([
            'status_code'=>'200',
            'response'=>'success',
            'message'=>'Updated Successfully'
        ]);

    }
     catch(\Exception $e){
        return response()->json([
            'status_code' =>'500',
            'message'=>'Something Went Wrong'
        ]);
        }
    }

    public function delete(Request $request,$id)
    {
        try{
            ProductCategory::where('id',$id)->delete();
            return response()->json([
                'status_code'=>'200',
                'response'=>'success',
                'message'=>'Deleted Successfully'
            ]);
        }
         catch(\Exception $e){
        return response()->json([
            'status_code' =>'500',
            'message'=>'Something Went Wrong'
        ]);
        }
    }


public function UpdateStatus(Request $request, $id)
   {
       try {
           $category = ProductCategory::find($id);
   
           if (!$category) {
               return response([
                   "status_code" => 404,
                   "message" => "category not found.",
               ]);
           }
   
           // Toggle status
           $category->status = $category->status == '1' ? '0' : '1';
           $category->save();
   
           return response([
               "status_code" => 200,
               "message" => "category status updated successfully.",
               "status" => $category->status, 
           ]);
   
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something went wrong.',
           ]);
       }
   }


}
