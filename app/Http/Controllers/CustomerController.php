<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Paginate;
class CustomerController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "customer_type" => "required",
            "name" => "required",
            "mobile" => "required|unique:customers,mobile",
            "email" => "required|unique:customers,email",
            "address" => "required",
            "city" => "required",
            "pincode" => "required",
            "contact_person" => "required",
            "designation" => "required",
            "state" => "required",
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response(["errors" => $errors->first()]);
        }
    
        try {
            $adminId=auth()->user()->id;
           
            // Get last customer's account number
            $lastCustomer = Customer::orderBy('account_number', 'desc')->first();
            $account_number = $lastCustomer ? $lastCustomer->account_number + 1 : 1;
    
            $model = new Customer();
            $model->admin_id = $adminId;
            $model->customer_type = $customer_type;
            $model->account_number = $account_number;
            $model->name = $request->name;
            $model->careof = $request->careof;
            $model->mobile = $request->mobile;
            $model->email = $request->email;
            $model->address = $request->address;
            $model->city = $request->city;
            $model->pincode = $request->pincode;
            $model->contact_person = $request->contact_person;
            $model->designation = $request->designation;
            $model->state = $request->state;
            $model->wallet = '0';
            $model->status = '1';
            $model->save();
    
            return response([
                "status_code" => "200",
                "message" => "Customer Added Successfully",
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
           $data = Customer::where('admin_id',$adminId)->paginate(10);
           return response([
               "status_code" => "200",
               "message" => "All Customers Data Fetched Successfully",
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
          $data=Customer::where('id',$id)->get();
           return response([
               "status_code" => "200",
               "message" => " Customers Data Fetched Successfully",
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
          $data=Customer::find($id);
          if($data){
            $data->delete();
           return response([
               "status_code" => "200",
               "message" => " Customers Data Delete Successfully",
               
           ]);
           }else{
            return response()->json([
                'status_code' =>404,
                'message' =>'Customer Not Found',
                ]);
                }
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something Went Wrong.',
           ]);
       }
   }


   public function UpdateStatus(Request $request, $id)
   {
       try {
           $customer = Customer::find($id);
   
           if (!$customer) {
               return response([
                   "status_code" => 404,
                   "message" => "Customer not found.",
               ]);
           }
   
           // Toggle status
           $customer->status = $customer->status == '1' ? '0' : '1';
           $customer->save();
   
           return response([
               "status_code" => 200,
               "message" => "Customer status updated successfully.",
               "status" => $customer->status, 
           ]);
   
       } catch (\Exception $e) {
           return response()->json([
               'status_code' => 500,
               'message' => 'Something went wrong.',
           ]);
       }
   }
   


   
  
   
public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        "customer_type" => "required",
        "name" => "required",
        "mobile" => "required|unique:customers,mobile,$id",
        "address" => "required",
        "city" => "required",
        "pincode" => "required",
        "contact_person" => "required",
        "designation" => "required",
        "state" => "required",
    ]);

    if ($validator->fails()) {
        return response([
            "status_code" => 422,
            "message" => $validator->errors()->first()
        ]);
    }

    // Check for duplicate account number but allow current record
    $account = Customer::where('account_number', $request->account_number)
        ->where('id', '!=', $id)
        ->first();

    if ($account) {
        return response()->json([
            'status_code' => 422,
            'message' => 'This Account Number is already allotted'
        ]);
    }

    // Prepare update data
    $model = [
        'customer_type' => $request->customer_type,
        'name' => $request->name,
        'careof' => $request->careof,
        'mobile' => $request->mobile,
        'account_number' => $request->account_number,
        'email' => $request->email,
        'address' => $request->address,
        'city' => $request->city,
        'pincode' => $request->pincode,
        'contact_person' => $request->contact_person,
        'designation' => $request->designation,
        'state' => $request->state,
    ];

    Customer::where('id', $id)->update($model);
    $data = Customer::find($id);

    return response([
        "status_code" => 200,
        "message" => "Customer data updated successfully",
        "data" => $data
    ]);
}

    
    
    
public function FetchCustomerDetail(Request $request)
{
    $validator = Validator::make($request->all(), [
        "account_number" => "required",
    ]);

    if ($validator->fails()) {
        return response()->json([
            "status_code" => 422,
            "message" => $validator->errors()->first()
        ]);
    }

    try {
        $account_number = $request->account_number;
        $adminId = auth()->user()->id;

        $customer = Customer::where('admin_id', $adminId)
            ->where('account_number', $account_number)
            ->first();

        if (!$customer) {
            return response()->json([
                "status_code" => 404,
                "message" => "Customer not found"
            ]);
        }

        if ($customer->status != '1') {
            return response()->json([
                "status_code" => 403,
                "message" => "Customer is not active"
            ]);
        }

        return response()->json([
            "status_code" => 200,
            "message" => "Customer data fetched successfully",
            "data" => $customer
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong',
            // 'error' => $e->getMessage() // for debugging if needed
        ]);
    }
}

   



}