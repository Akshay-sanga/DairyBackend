<?php

namespace App\Http\Controllers;

use App\Models\MilkDispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Paginate;
class MilkDispatchController extends Controller
{
    public function submit(Request $request)
   {
    $validate = Validator::make($request->all(), [
        'dispatch_date' => 'required',
        'shift' => 'required',
        'head_dairy_name' => 'required',
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
        $model->head_dairy_name=$request->head_dairy_name;
        $model->vehicle_no=$request->vehicle_no;
        $model->total_qty=$request->total_qty;
        $model->total_amount=$request->total_amount;
         $model->milk_details = json_encode($request->milk_details);
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


    public function update(Request $request,$id)
    {
         $validate = Validator::make($request->all(), [
       'dispatch_date' => 'required',
        'shift' => 'required',
        'head_dairy_name' => 'required',
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
        $model['dispatch_date']=$request->post('dispatch_date');
        $model['shift']=$request->post('shift');
        $model['head_dairy_name']=$request->post('head_dairy_name');
        $model['vehicle_no']=$request->post('vehicle_no');
        $model['total_qty']=$request->post('total_qty');
        $model['total_amount']=$request->post('total_amount');
        $model['milk_details']=json_encode($request->post('milk_details'));
        $model['notes']=$request->post('notes');
        MilkDispatch::where('id',$id)->update($model);
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
