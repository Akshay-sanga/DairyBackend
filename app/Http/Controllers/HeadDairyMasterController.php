<?php

namespace App\Http\Controllers;

use App\Models\HeadDairyMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HeadDairyMasterController extends Controller
{
     public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "dairy_name"=>"required",
            "contact_person"=>"required",
            "mobile"=>"required",
            "address"=>"required",
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response(["errors" => $errors->first()]);
        }

        try{
            $adminId = auth()->user()->id;
            $model = new HeadDairyMaster();
            $model->admin_id=$adminId;
            $model->dairy_name=$request->dairy_name;
            $model->contact_person=$request->contact_person;
            $model->mobile=$request->mobile;
            $model->address=$request->address;
            $model->wallet='0';
            $model->status='1';
            $model->save();
             return response([
                "status_code" => "200",
                "message" => "Head Dairy Added Successfully",
                "data" => $model,
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }

    public function all()
    {
        try{
            $adminId = auth()->user()->id;
            $data = HeadDairyMaster::where('admin_id',$adminId)->orderBy('id','desc')->get();
             return response([
                "status_code" => "200",
                "message" => "All Head Dairy Data",
                "data" => $data,
            ]);
        }
         catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }
    public function fetch()
    {
        try{
            $adminId = auth()->user()->id;
            $data = HeadDairyMaster::where('admin_id',$adminId)->where('status','1')->first();
             return response([
                "status_code" => "200",
                "message" => "Head Dairy Data",
                "data" => $data,
            ]);
        }
         catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }

    public function edit(Request $request,$id)
    {
          try{
            $data = HeadDairyMaster::where('id',$id)->first();
             return response([
                "status_code" => "200",
                "message" => "Head Dairy Data",
                "data" => $data,
            ]);
        }
         catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            "dairy_name"=>"required",
            "contact_person"=>"required",
            "mobile"=>"required",
            "address"=>"required",
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response(["errors" => $errors->first()]);
        }

        try{
    
            $model['dairy_name']=$request->post('dairy_name');
            $model['contact_person']=$request->post('contact_person');
            $model['mobile']=$request->post('mobile');
            $model['address']=$request->post('address');
            HeadDairyMaster::where('id',$id)->update($model);
            return response([
                "status_code" => "200",
                "message" => "Head Dairy Data Updated Successfully",
                "data" => $model,
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }



       public function delete(Request $request,$id)
    {
          try{
            $data = HeadDairyMaster::find($id);
            $data->delete();
             return response([
                "status_code" => "200",
                "message" => "Head Dairy Data Deleted",
            ]);
        }
         catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }


     public function UpdateStatus(Request $request, $id)
   {
       try {
           $data = HeadDairyMaster::find($id);
   
           if (!$data) {
               return response([
                   "status_code" => 404,
                   "message" => "data not found.",
               ]);
           }
   
           // Toggle status
           $data->status = $data->status == '1' ? '0' : '1';
           $data->save();
   
           return response([
               "status_code" => 200,
               "message" => "data status updated successfully.",
               "status" => $data->status, 
           ]);
   
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something went wrong.',
           ]);
       }
   }
   






}
