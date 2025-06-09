<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Customer;
use App\Models\ProductSale;
use App\Models\MilkCollection;
use App\Models\Payment;
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
        $accountNumber = $request->customer_account_number;

        // Convert input d-m-Y to Y-m-d for DB query on DATE fields
        $startDateForDateColumn = \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
        $endDateForDateColumn = \Carbon\Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');

        // Dates for string-based d-m-Y columns
        $startDateInput = $request->start_date;
        $endDateInput = $request->end_date;

        /** 1. Milk Collection — (Y-m-d format in DB) */
        $milkCollections = MilkCollection::where('customer_account_number', $accountNumber)
            ->whereBetween('date', [$startDateForDateColumn, $endDateForDateColumn])
            ->get();

        $milkTotalCollecttion = $milkCollections->sum('quantity');
        $milkTotal = $milkCollections->sum('total_amount');

        /** 2. Product Sale — (stored as string in d-m-Y) */
        $productSales = ProductSale::where('customer_account_number', $accountNumber)
            ->whereRaw("STR_TO_DATE(`date`, '%d-%m-%Y') BETWEEN STR_TO_DATE(?, '%d-%m-%Y') AND STR_TO_DATE(?, '%d-%m-%Y')", [
                $startDateInput,
                $endDateInput
            ])
            ->get();

        $productTotal = $productSales->sum('total');

        /** 3. Payment — (stored as string in d-m-Y) */
        $payments = Payment::where('customer_account_number', $accountNumber)
            ->whereRaw("STR_TO_DATE(`date`, '%d-%m-%Y') BETWEEN STR_TO_DATE(?, '%d-%m-%Y') AND STR_TO_DATE(?, '%d-%m-%Y')", [
                $startDateInput,
                $endDateInput
            ])
            ->get();

        $paymentTotal = $payments->sum('amount');

        /** 4. Customer Wallet */
        $customer = Customer::where('account_number', $accountNumber)->first();

        return response()->json([
            'status_code' => 200,
            'message' => 'Customer report fetched successfully',
            'data' => [
                'milk_collections' => $milkCollections,
                'total_milk_collections' => $milkTotalCollecttion,
                'milk_total_amount' => $milkTotal,

                'product_sales' => $productSales,
                'product_total_amount' => $productTotal,

                'payments' => $payments,
                'payment_total_amount' => $paymentTotal,

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



    
    
    
//   public function fetchCustomerReport(Request $request)
// {
//     $request->validate([
//         'customer_account_number' => 'required|string',
//         'start_date' => 'required|date',
//         'end_date' => 'required|date',
//     ]);

//     // try {
//         $accountNumber = $request->customer_account_number;
//         $startDate = $request->start_date;
//         $endDate = $request->end_date;
        
//         // return ['A'=>$accountNumber,'S'=>$startDate,'E'=>$endDate];

//         // 1. Milk Collection
//         $milkCollections = MilkCollection::where('customer_account_number', $accountNumber)
//             ->whereBetween('date', [$startDate, $endDate])
//             ->get();
//             // return $milkCollections;

//         $milkTotalCollecttion = $milkCollections->sum('quantity');
//         $milkTotal = $milkCollections->sum('total_amount');

//         // 2. Product Sale
//         $productSales = ProductSale::where('customer_account_number', $accountNumber)
//             ->whereBetween('date', [$startDate, $endDate])
//             ->get();

//         $productTotal = $productSales->sum('total_amount');

//         // 3. Payment
//         $payments = Payment::where('customer_account_number', $accountNumber)
//             ->whereBetween('date', [$startDate, $endDate])
//             ->get();

//         $paymentTotal = $payments->sum('amount');

//         // 4. Customer Wallet
//         $customer = Customer::where('account_number', $accountNumber)->first();
        

//         return response()->json([
//             'status_code' => 200,
//             'message' => 'Customer report fetched successfully',
//             'data' => [
//                 'milk_collections' => $milkCollections,
//                 'total_milk_collections' => $milkTotalCollecttion,
//                 'milk_total_amount' => $milkTotal,

//                 'product_sales' => $productSales,
//                 'product_total_amount' => $productTotal,

//                 'payments' => $payments,
//                 'payment_total_amount' => $paymentTotal,

//                 'customer_wallet' => $customer ? $customer->wallet : null,
//                 'customer_name' => $customer->name,
//             ]
//         ]);
//     // } catch (\Exception $e) {
//     //     return response()->json([
//     //         'status_code' => 500,
//     //         'message' => 'Something went wrong.',
//     //         'error' => $e->getMessage()
//     //     ]);
//     // }
// }

}
