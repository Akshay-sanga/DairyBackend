<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\HeadDairyMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
   public function submit(Request $request)
{
    $isCustomerPayment = $request->has('customer_account_number');
    $isHeadDairyPayment = $request->has('head_dairy_id');

    if (!$isCustomerPayment && !$isHeadDairyPayment) {
        return response()->json([
            'status_code' => 422,
            'message' => 'Invalid data. Either customer_account_number or head_dairy_id is required.'
        ]);
    }

    // Validation rules
    $rules = [
        'date' => 'required|date',
        'amount' => 'required|numeric',
        'mode' => 'required|string',
        'note' => 'nullable|string',
    ];

    if ($isCustomerPayment) {
        $rules = array_merge($rules, [
            'customer_account_number' => 'required|string',
            'type' => 'required|in:received,given',
        ]);
    }

    if ($isHeadDairyPayment) {
        $rules = array_merge($rules, [
            'head_dairy_id' => 'required|integer',
        ]);
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status_code' => 400,
            'message' => $validator->messages()->first()
        ]);
    }

    try {
        // Save payment
        $adminId = auth()->user()->id;
        $payment = new Payment();
        $payment->admin_id = $adminId ?? null;
        $payment->head_dairy_id = $request->head_dairy_id ?? null;
        $payment->customer_account_number = $request->customer_account_number ?? null;
        $payment->date = $request->date;
        $payment->amount = $request->amount;
        $payment->type = $request->type ?? null;
        $payment->mode = $request->mode;
        $payment->note = $request->note ?? null;
        $payment->save();

        // Update wallet
        if ($isCustomerPayment) {
            $customer = Customer::where('account_number', $request->customer_account_number)->first();
            if ($customer) {
                if ($request->type == 'received') {
                    $customer->wallet += $request->amount;
                } elseif ($request->type == 'given') {
                    $customer->wallet -= $request->amount;
                }
                $customer->save();
            }
        } elseif ($isHeadDairyPayment) {
            $dairy = HeadDairyMaster::find($request->head_dairy_id);
            if ($dairy) {
                $dairy->wallet -= $request->amount;
                $dairy->save();
            }
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Payment saved successfully',
            'data' => $payment
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}

public function allcustomerPayment()
{
    try {
        $adminId = auth()->user()->id;

        $payments = Payment::with('customer')->where('admin_id', $adminId)
            ->whereNotNull('customer_account_number') // Filter payments with customer_account_number present
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Customer payments fetched successfully',
            'data' => $payments
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}


public function allheaddairyPayment()
{
    try {
        $adminId = auth()->user()->id;

        $payments = Payment::with('headDairy')->where('admin_id', $adminId)
            ->whereNotNull('head_dairy_id') 
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Head Dairy payments fetched successfully',
            'data' => $payments
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}


public function edit(Request $request,$id)
{
    try {
        $data = Payment::where('id',$id)->first();
         return response()->json([
            'status_code' => 200,
            'message' => ' payments data fetched successfully',
            'data' => $data
        ]);
    }
         catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}


public function update(Request $request, $id)
{
    $isCustomerPayment = $request->has('customer_account_number');
    $isHeadDairyPayment = $request->has('head_dairy_id');

    if (!$isCustomerPayment && !$isHeadDairyPayment) {
        return response()->json([
            'status_code' => 422,
            'message' => 'Invalid data. Either customer_account_number or head_dairy_id is required.'
        ]);
    }

    // Validation
    $rules = [
        'date' => 'required|date',
        'amount' => 'required|numeric',
        'mode' => 'required|string',
        'note' => 'nullable|string',
    ];

    if ($isCustomerPayment) {
        $rules = array_merge($rules, [
            'customer_account_number' => 'required|string',
            'type' => 'required|in:received,given',
        ]);
    }

    if ($isHeadDairyPayment) {
        $rules = array_merge($rules, [
            'head_dairy_id' => 'required|integer',
        ]);
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status_code' => 400,
            'message' => $validator->messages()->first()
        ]);
    }

    try {
        $payment = Payment::findOrFail($id);
        $previousAmount = $payment->amount;
        $previousType = $payment->type;

        // Update payment data
        $payment->date = $request->date;
        $payment->amount = $request->amount;
        $payment->type = $request->type ?? null;
        $payment->mode = $request->mode;
        $payment->note = $request->note ?? null;
        $payment->customer_account_number = $request->customer_account_number ?? null;
        $payment->head_dairy_id = $request->head_dairy_id ?? null;
        $payment->save();

        // Adjust wallet
        if ($isCustomerPayment) {
            $customer = Customer::where('account_number', $request->customer_account_number)->first();
            if ($customer) {
                // Reverse previous
                if ($previousType == 'received') {
                    $customer->wallet -= $previousAmount;
                } elseif ($previousType == 'given') {
                    $customer->wallet += $previousAmount;
                }

                // Apply new
                if ($request->type == 'received') {
                    $customer->wallet += $request->amount;
                } elseif ($request->type == 'given') {
                    $customer->wallet -= $request->amount;
                }

                $customer->save();
            }
        } elseif ($isHeadDairyPayment) {
            $dairy = HeadDairyMaster::find($request->head_dairy_id);
            if ($dairy) {
                // Reverse previous
                $dairy->wallet -= $previousAmount;

                // Apply new
                $dairy->wallet += $request->amount;

                $dairy->save();
            }
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Payment updated successfully',
            'data' => $payment
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}

public function delete($id)
{
    try {
        $payment = Payment::findOrFail($id);

        // Adjust wallet before deletion
        if ($payment->customer_account_number) {
            $customer = Customer::where('account_number', $payment->customer_account_number)->first();
            if ($customer) {
                if ($payment->type == 'received') {
                    $customer->wallet -= $payment->amount;
                } elseif ($payment->type == 'given') {
                    $customer->wallet += $payment->amount;
                }
                $customer->save();
            }
        } elseif ($payment->head_dairy_id) {
            $dairy = HeadDairyMaster::find($payment->head_dairy_id);
            if ($dairy) {
                // Reverse the amount
                $dairy->wallet += $payment->amount;
                $dairy->save();
            }
        }

        // Delete the payment record
        $payment->delete();

        return response()->json([
            'status_code' => 200,
            'message' => 'Payment deleted and wallet adjusted successfully',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}


}
