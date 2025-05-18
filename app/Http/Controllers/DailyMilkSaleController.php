<?php

namespace App\Http\Controllers;

use App\Models\DailyMilkSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DailyMilkSaleController extends Controller
{
   public function submit(Request $request)
   {
    $validate = Validator::make($request->all(), [
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
            'status_code' => 400, // Use 400 for validation errors
            'response' => 'error',
            'data' => new \stdClass(),
            'message' => $validate->messages()->first(),
        ]);
    }

    try{
        $adminId=auth()->user()->id;
        $model = new DailyMilkSale();
        $model->admin_id=$adminId;
        $model->sale_date=$request->sale_date;
        $model->shift=$request->shift;
        $model->milk_type=$request->milk_type;
        $model->quentity=$request->quentity;
        $model->rate_per_liter=$request->rate_per_liter;
        $model->total_amount=$request->total_amount;
        $model->payment_mode=$request->payment_mode;
        $model->notes=$request->notes;
        $model->save();
        return response()->json([
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
        $data = DailyMilkSale::where('admin_id',$adminId)->orderBy('id','desc')->get();
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


    public function update(Request $request,$id)
    {
         $validate = Validator::make($request->all(), [
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
            'status_code' => 400, // Use 400 for validation errors
            'response' => 'error',
            'data' => new \stdClass(),
            'message' => $validate->messages()->first(),
        ]);
    }

    try{
        $model['sale_date']=$request->post('sale_date');
        $model['shift']=$request->post('shift');
        $model['milk_type']=$request->post('milk_type');
        $model['quentity']=$request->post('quentity');
        $model['rate_per_liter']=$request->post('rate_per_liter');
        $model['total_amount']=$request->post('total_amount');
        $model['payment_mode']=$request->post('payment_mode');
        $model['notes']=$request->post('notes');
        DailyMilkSale::where('id',$id)->update($model);
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
            DailyMilkSale::where('id',$id)->delete();
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

}
