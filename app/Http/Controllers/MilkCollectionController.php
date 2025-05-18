<?php

namespace App\Http\Controllers;

use App\Models\MilkCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MilkCollectionController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "customer_account_number" => "required",
            "milk_type" => "required",
            "quantity" => "required",
            "clr" => "required",
            "fat" => "required",
            "snf" => "required",
            "base_rate" => "required",
            "other_price" => "required",
            "name" => "required",
            "spouse" => "required",
            "mobile" => "required",
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response(["errors" => $errors->first()]);
        }
    
        try {
           $adminId=auth()->user()->id;
    
            $model = new MilkCollection();
            $model->admin_id=$adminId;
            $model->customer_account_number = $request->customer_account_number;
            $model->milk_type = $request->milk_type;
            $model->quantity = $request->quantity;
            $model->clr = $request->clr;
            $model->fat = $request->fat;
            $model->snf= $request->snf;
            $model->base_rate = $request->base_rate;
            $model->other_price= $request->other_price;
            $model->name = $request->name;
            $model->spouse = $request->spouse;
            $model->mobile = $request->mobile;
            $model->save();
    
            return response([
                "status_code" => "200",
                "message" => "Milk Collection Added Successfully",
                "data" => $model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }
    


   public function all()
   {
       try {
        $adminId=auth()->user()->id;
           $data = MilkCollection::where('admin_id',$adminId)->paginate(10); // remove ->all()
           return response([
               "status_code" => "200",
               "message" => "All Milk Collection Data Fetched Successfully",
               "data" => $data
           ]);
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something Went Wrong.',
           ]);
       }
   }


   public function edit(Request $request,$id)
   {
       try {
          $data=MilkCollection::where('id',$id)->get();
           return response([
               "status_code" => "200",
               "message" => " Milk Collection Data Fetched Successfully",
               "data" => $data
           ]);
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something Went Wrong.',
           ]);
       }
   }

   public function delete(Request $request,$id)
   {
       try {
          $data=MilkCollection::find($id);
          if($data){
            $data->delete();
           return response([
               "status_code" => "200",
               "message" => " Milk Collection Data Delete Successfully",
               
           ]);
           }else{
            return response()->json([
                'status_code' =>404,
                'message' =>'Collection Not Found',
                ]);
                }
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something Went Wrong.',
           ]);
       }
   }

   public function update(Request $request, $id)
   {
       $validator = Validator::make($request->all(), [
        "customer_account_number" => "required",
        "milk_type" => "required",
        "quantity" => "required",
        "clr" => "required",
        "fat" => "required",
        "snf" => "required",
        "base_rate" => "required",
        "other_price" => "required",
        "name" => "required",
        "spouse" => "required",
        "mobile" => "required",
       ]);
   
       if ($validator->fails()) {
           $errors = $validator->errors();
           return response([
               "status_code" => 422,
               "message" => $errors->first()
           ]);
       }
   
       try {
           $model = [
               'customer_account_number' => $request->customer_account_number,
               'milk_type' => $request->milk_type,
               'quantity' => $request->quantity,
               'clr' => $request->clr,
               'fat' => $request->fat,
               'snf' => $request->snf,
               'base_rate' => $request->base_rate,
               'other_price' => $request->other_price,
               'name' => $request->name,
               'spouse' => $request->spouse,
               'mobile' => $request->mobile,
           ];
   
           MilkCollection::where('id', $id)->update($model);
           $data = MilkCollection::find($id); // Fetch updated data
   
           return response([
               "status_code" => 200,
               "message" => "Collection data updated successfully",
               "data" => $data
           ]);
   
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something went wrong.',
           ]);
       }
    }
   

}
