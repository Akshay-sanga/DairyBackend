<?php

namespace App\Http\Controllers;

use App\Models\MilkCollection;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MilkCollectionController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "customer_account_number" => "required",
            "shift" => "required",
            "milk_type" => "required",
            "date" => "required",
            "quantity" => "required",
            "clr" => "required",
            "fat" => "required",
            "snf" => "required",
            "base_rate" => "required",
            "other_price" => "required",
            "total_amount" => "required",
            "name" => "required",
            "careof" => "required",
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
            $model->date = $request->date;
            $model->shift = $request->shift;
            $model->milk_type = $request->milk_type;
            $model->quantity = $request->quantity;
            $model->clr = $request->clr;
            $model->fat = $request->fat;
            $model->snf= $request->snf;
            $model->base_rate = $request->base_rate;
            $model->other_price= $request->other_price;
            $model->total_amount= $request->total_amount;
            $model->name = $request->name;
            $model->careof = $request->careof;
            $model->mobile = $request->mobile;
            $model->save();

            $customerAccountNumber  = $model->customer_account_number;

           $customer = Customer::where('account_number', $customerAccountNumber)->first();
            if ($customer) {
                $customer->wallet += $model->total_amount;
                $customer->save(); // 🔁 MUST SAVE after update
            }


    
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
           $data = MilkCollection::with('customer')->where('admin_id',$adminId)->paginate(10); // remove ->all()
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
        "date" => "date",
        "shift" => "required",
        "milk_type" => "required",
        "quantity" => "required",
        "clr" => "required",
        "fat" => "required",
        "snf" => "required",
        "base_rate" => "required",
        "other_price" => "required",
        "total_amount" => "required",
        "name" => "required",
        "careof" => "required",
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
        $collection = MilkCollection::find($id);

        if (!$collection) {
            return response([
                "status_code" => 404,
                "message" => "Record not found."
            ]);
        }

        $oldAmount = $collection->total_amount;

        $model = [
            'customer_account_number' => $request->customer_account_number,
            'milk_type' => $request->milk_type,
            'date' => $request->date,
            'shift' => $request->shift,
            'quantity' => $request->quantity,
            'clr' => $request->clr,
            'fat' => $request->fat,
            'snf' => $request->snf,
            'base_rate' => $request->base_rate,
            'other_price' => $request->other_price,
            'total_amount' => $request->total_amount,
            'name' => $request->name,
            'careof' => $request->careof,
            'mobile' => $request->mobile,
        ];

        MilkCollection::where('id', $id)->update($model);

        $customer = Customer::where('account_number', $request->customer_account_number)->first();

        if ($customer) {
            $walletDiff = $request->total_amount - $oldAmount;
            $customer->wallet += $walletDiff;
            $customer->save();
        }

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
