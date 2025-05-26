<?php

namespace App\Http\Controllers;

use App\Models\MilkDispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Paginate;
class MilkDispatchController extends Controller
{
    public function submit(Request $request)
   {
    $validate = Validator::make($request->all(), [
        'dispatch_date' => 'required',
        'shift' => 'required',
        'head_dairy_id' => 'required',
        'vehicle_no' => 'required',
        'total_qty' => 'required',
        'total_amount' => 'required',
         'milk_details' => 'required|array',
        'notes' => 'required',
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
        $model = new MilkDispatch();
        $model->admin_id=$adminId;
        $model->dispatch_date=$request->dispatch_date;
        $model->shift=$request->shift;
        $model->head_dairy_id=$request->head_dairy_id;
        $model->vehicle_no=$request->vehicle_no;
        $model->total_qty=$request->total_qty;
        $model->total_amount=$request->total_amount;
         $model->milk_details = json_encode($request->milk_details);
        $model->notes=$request->notes;
        $model->save();

        $headDairy = HeadDairyMaster::where('id',$model->head_dairy_id)->first();
        $headDairy->wallet += $model->total_amount;
        $headDairy->update();

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
        $data = MilkDispatch::where('admin_id',$adminId)->orderBy('id','desc')->paginate(10);
          foreach ($data as $item) {
            $item->milk_details = json_decode($item->milk_details);
        }
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
            $data=MilkDispatch::where('id',$id)->first();
             $data->milk_details = json_decode($data->milk_details);
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
        'dispatch_date' => 'required',
        'shift' => 'required',
        'head_dairy_id' => 'required',
        'vehicle_no' => 'required',
        'total_qty' => 'required',
        'total_amount' => 'required',
        'milk_details' => 'required|array',
        'notes' => 'required',
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
        DB::beginTransaction();

        $dispatch = MilkDispatch::find($id);
        if (!$dispatch) {
            return response()->json([
                'status_code' => 404,
                'response' => 'error',
                'message' => 'Record not found',
            ]);
        }

        // Previous amount
        $previousAmount = $dispatch->total_amount;

        // Update dispatch details
        $dispatch->dispatch_date = $request->dispatch_date;
        $dispatch->shift = $request->shift;
        $dispatch->head_dairy_id = $request->head_dairy_id;
        $dispatch->vehicle_no = $request->vehicle_no;
        $dispatch->total_qty = $request->total_qty;
        $dispatch->total_amount = $request->total_amount;
        $dispatch->milk_details = json_encode($request->milk_details);
        $dispatch->notes = $request->notes;
        $dispatch->save();

        // Update Head Dairy Wallet
        $walletDiff = $request->total_amount - $previousAmount;
        $headDairy = HeadDairyMaster::find($dispatch->head_dairy_id);
        $headDairy->wallet += $walletDiff;
        $headDairy->save();

        DB::commit();

        return response()->json([
            'status_code' => 200,
            'response' => 'success',
            'message' => 'Updated Successfully'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status_code' => 500,
            'response' => 'error',
            'message' => 'Something went wrong',
            // 'error' => $e->getMessage(), // Uncomment this for debug
        ]);
    }
}

    public function delete(Request $request,$id)
    {
        try{
            MilkDispatch::where('id',$id)->delete();
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
