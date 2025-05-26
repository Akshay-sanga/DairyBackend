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
   public function fetchCustomerRepost(Request $request)
{
    $request->validate([
        'customer_account_number' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ]);

    // try {
        $accountNumber = $request->customer_account_number;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // 1. Milk Collection
        $milkCollections = MilkCollection::where('customer_account_number', $accountNumber)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $milkTotal = $milkCollections->sum('total_amount');

        // 2. Product Sale
        $productSales = ProductSale::where('customer_account_number', $accountNumber)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $productTotal = $productSales->sum('total_amount');

        // 3. Payment
        $payments = Payment::where('customer_account_number', $accountNumber)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $paymentTotal = $payments->sum('amount');

        // 4. Customer Wallet
        $customer = Customer::where('account_number', $accountNumber)->first();

        return response()->json([
            'status_code' => 200,
            'message' => 'Customer report fetched successfully',
            'data' => [
                'milk_collections' => $milkCollections,
                'milk_total_amount' => $milkTotal,

                'product_sales' => $productSales,
                'product_total_amount' => $productTotal,

                'payments' => $payments,
                'payment_total_amount' => $paymentTotal,

                'customer_wallet' => $customer ? $customer->wallet : null,
            ]
        ]);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         'status_code' => 500,
    //         'message' => 'Something went wrong.',
    //         'error' => $e->getMessage()
    //     ]);
    // }
}

}
