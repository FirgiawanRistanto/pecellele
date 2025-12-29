<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order; // Import Order model
use Carbon\Carbon; // Import Carbon for date manipulation
use Illuminate\Http\Request;
use PDF; // Import PDF facade

class ReportController extends Controller
{
    public function generatePdf(Request $request)
    {
        $filter = $request->query('filter', 'daily'); // Default to daily

        // Fetch all orders with their items and menu details
        $allOrders = Order::with('items.menu')->orderBy('created_at', 'desc')->get();
        // $filteredOrders = collect(); // Removed for testing
        $reportPeriodText = 'Harian';

        $now = Carbon::now(); // Get current time once

        // Temporarily assign allOrders to filteredOrders to test PDF rendering
        $ordersToReport = $allOrders; 
        $reportPeriodText = 'Semua Periode (Testing)';


        $totalSales = $ordersToReport->sum('total');

        $data = [
            'orders' => $ordersToReport,
            'totalSales' => $totalSales,
            'reportPeriodText' => $reportPeriodText,
            'dateGenerated' => Carbon::now()->format('d F Y H:i:s'), // Generate current time for the PDF generation
        ];

        // Load the Blade view and pass data to it
        $pdf = PDF::loadView('pdf.report', $data);

        // Return the generated PDF for download
        return $pdf->download('laporan_penjualan_' . $filter . '_' . Carbon::now()->format('YmdHis') . '.pdf');
    }
}
