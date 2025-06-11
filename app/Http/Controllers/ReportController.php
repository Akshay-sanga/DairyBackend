<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Customer;
use App\Models\ProductSale;
use App\Models\DailyMilkSale;
use App\Models\MilkCollection;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    
public function fetchCustomerReport(Request $request)
{
    $request->validate([
        'customer_account_number' => 'required|string',
        'start_date' => 'required|date_format:d-m-Y',
        'end_date' => 'required|date_format:d-m-Y',
    ]);

    try {
        $adminId = auth()->user()->id;
        $accountNumber = $request->customer_account_number;

        // Convert to Y-m-d for DB columns using DATE type
        $startDateForDateColumn = \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
        $endDateForDateColumn = \Carbon\Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');

        // Use original format for STR_TO_DATE queries
        $startDateInput = $request->start_date;
        $endDateInput = $request->end_date;

        /** 1. Milk Collection */
        $milkCollections = MilkCollection::where('admin_id',$adminId)->where('customer_account_number', $accountNumber)
            ->whereBetween('date', [$startDateForDateColumn, $endDateForDateColumn])
            ->get();

        $milkTotalCollection = $milkCollections->sum('quantity');
        $milkTotal = $milkCollections->sum('total_amount');

        /** 2. Product Sale */
        $productSales = ProductSale::where('admin_id',$adminId)->where('customer_account_number', $accountNumber)
            ->whereRaw("STR_TO_DATE(`date`, '%d-%m-%Y') BETWEEN STR_TO_DATE(?, '%d-%m-%Y') AND STR_TO_DATE(?, '%d-%m-%Y')", [
                $startDateInput,
                $endDateInput
            ])
            ->get();

        $productTotal = $productSales->sum('total');

        /** 3. Payment */
        $payments = Payment::where('admin_id',$adminId)->where('customer_account_number', $accountNumber)
            ->whereRaw("STR_TO_DATE(`date`, '%d-%m-%Y') BETWEEN STR_TO_DATE(?, '%d-%m-%Y') AND STR_TO_DATE(?, '%d-%m-%Y')", [
                $startDateInput,
                $endDateInput
            ])
            ->get();

        $paymentTotal = $payments->sum('amount');

        /** 4. Customer Wallet & Buyer Milk Sale */
        $customer = Customer::where('admin_id',$adminId)->where('account_number', $accountNumber)->first();
        $buyerMilk = collect(); // empty collection by default
        $buyerMilkQty = 0;
        $buyerMilkTotal = 0;

        if ($customer && $customer->customer_type == 'Buyer') {
            $buyerMilk = DailyMilkSale::where('admin_id',$adminId)->where('customer_account_number', $accountNumber)
                ->whereRaw("STR_TO_DATE(`sale_date`, '%d-%m-%Y') BETWEEN STR_TO_DATE(?, '%d-%m-%Y') AND STR_TO_DATE(?, '%d-%m-%Y')", [
                    $startDateInput,
                    $endDateInput
                ])
                ->get();

            $buyerMilkQty = $buyerMilk->sum('quentity');
            $buyerMilkTotal = $buyerMilk->sum('total_amount');
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Customer report fetched successfully',
            'data' => [
                'milk_collections' => $milkCollections,
                'total_milk_collections' => $milkTotalCollection,
                'milk_total_amount' => $milkTotal,

                'product_sales' => $productSales,
                'product_total_amount' => $productTotal,

                'payments' => $payments,
                'payment_total_amount' => $paymentTotal,

                'buyer_milk_sales' => $buyerMilk,
                'buyer_milk_total_quantity' => $buyerMilkQty,
                'buyer_milk_total_amount' => $buyerMilkTotal,

                'customer_wallet' => $customer?->wallet,
                'customer_name' => $customer?->name,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage()
        ]);
    }
}

public function fetchPaymentRegister(Request $request)
{
    $request->validate([
        'start_date' => 'required|date_format:d-m-Y',
        'end_date' => 'required|date_format:d-m-Y',
    ]);

    try {
        $adminId = auth()->user()->id;
        $startDateForDateColumn = Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
        $endDateForDateColumn = Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');

        // Fetch grouped milk collections
        $milkCollections = MilkCollection::selectRaw('
                customer_account_number,
                SUM(quantity) as total_quantity,
                SUM(total_amount) as total_amount
            ')
            ->where('admin_id', $adminId)
            ->whereBetween('date', [$startDateForDateColumn, $endDateForDateColumn])
            ->groupBy('customer_account_number')
            ->get();

        // Manually load customer relation
        $milkCollections->load('customer');

        // Format the response with customer name
        $result = $milkCollections->map(function ($item) {
            return [
                'customer_account_number' => $item->customer_account_number,
                'customer_name' => optional($item->customer)->name ?? 'N/A',
                'total_quantity' => $item->total_quantity,
                'total_amount' => $item->total_amount,
            ];
        });

        return response()->json([
            'status_code' => 200,
            'data' => $result,
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
