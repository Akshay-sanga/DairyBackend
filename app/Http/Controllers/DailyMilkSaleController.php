<?php

namespace App\Http\Controllers;

use App\Models\DailyMilkSale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Paginate;

class DailyMilkSaleController extends Controller
{
   public function submit(Request $request)
{
    $validate = Validator::make($request->all(), [
        'customer_name' => 'required',
        'sale_date' => 'required',
        'shift' => 'required',
        'milk_type' => 'required',
        'quentity' => 'required',
        'rate_per_liter' => 'required',
        'total_amount' => 'required',
        'payment_mode' => 'required',
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
        $adminId = auth()->user()->id;

        $model = new DailyMilkSale();
        $model->admin_id = $adminId;
        $model->customer_account_number = $request->customer_account_number;
        $model->customer_name = $request->customer_name;
        $model->sale_date = $request->sale_date;
        $model->shift = $request->shift;
        $model->milk_type = $request->milk_type;
        $model->quentity = $request->quentity;
        $model->rate_per_liter = $request->rate_per_liter;
        $model->total_amount = $request->total_amount;
        $model->payment_mode = $request->payment_mode;
        $model->notes = $request->notes;
        $model->save();

      
        if (!empty($model->customer_account_number) && $model->payment_mode === 'pending') {
            $wallet = Customer::where('account_number', $model->customer_account_number)->first();
            if ($wallet) {
                $wallet->wallet = $wallet->wallet - $model->total_amount;
                $wallet->save();
            }
        }

        return response()->json([
            'status_code' => 200,
            'response' => 'success',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong',
            'error' => $e->getMessage(), 
        ]);
    }
}


   public function all()
   {
    try{
        $adminId=auth()->user()->id;
        $data = DailyMilkSale::with('customer')->where('admin_id',$adminId)->orderBy('id','desc')->paginate(10);
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
            $data=DailyMilkSale::where('id',$id)->first();
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


   public function update(Request $request, $id)
{
    $validate = Validator::make($request->all(), [
        'customer_name' => 'required',
        'sale_date' => 'required',
        'shift' => 'required',
        'milk_type' => 'required',
        'quentity' => 'required',
        'rate_per_liter' => 'required',
        'total_amount' => 'required',
        'payment_mode' => 'required',
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
        $model = DailyMilkSale::findOrFail($id);

        
        $oldTotal = $model->total_amount;
        $oldPaymentMode = $model->payment_mode;

    
        $model->customer_account_number = $request->customer_account_number;
        $model->customer_name = $request->customer_name;
        $model->sale_date = $request->sale_date;
        $model->shift = $request->shift;
        $model->milk_type = $request->milk_type;
        $model->quentity = $request->quentity;
        $model->rate_per_liter = $request->rate_per_liter;
        $model->total_amount = $request->total_amount;
        $model->payment_mode = $request->payment_mode;
        $model->notes = $request->notes;
        $model->save();

      
        if (!empty($model->customer_account_number)) {
            $customer = Customer::where('account_number', $model->customer_account_number)->first();
            if ($customer) {
                if ($oldPaymentMode == 'pending') {
                    
                    $customer->wallet += $oldTotal;
                }
                if ($model->payment_mode == 'pending') {
                   
                    $customer->wallet -= $model->total_amount;
                }
                $customer->save();
            }
        }

        return response()->json([
            'status_code' => 200,
            'response' => 'success',
            'message' => 'Updated Successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something Went Wrong',
           
        ]);
    }
}


   public function delete(Request $request, $id)
{
    try {
        $model = DailyMilkSale::findOrFail($id);

       
        if (!empty($model->customer_account_number) && $model->payment_mode == 'pending') {
            $customer = Customer::where('account_number', $model->customer_account_number)->first();
            if ($customer) {
                // Add back the pending amount to wallet
                $customer->wallet += $model->total_amount;
                $customer->save();
            }
        }

        $model->delete();

        return response()->json([
            'status_code' => 200,
            'response' => 'success',
            'message' => 'Deleted Successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something Went Wrong',
           
        ]);
    }
}


}
