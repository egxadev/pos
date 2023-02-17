<?php

namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // RETURN VIEW
        return Inertia::render('Apps/Sales/Index');
    }

    /**
     * filter
     *
     * @param  mixed $request
     * @return void
     */
    public function filter(Request $request)
    {
        // VALIDATE REQUEST
        $this->validate($request, [
            'start_date'    => 'required',
            'end_date'      => 'required',
        ]);

        // GET DATA SALES BY RANGE DATE
        $sales = Transaction::with('cashier', 'customer')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->get();

        // GET TOTAL SALES BY RANGE DATE
        $total = Transaction::whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->sum('grand_total');

        // RETURN VIEW
        return Inertia::render('Apps/Sales/Index', [
            'sales' => $sales,
            'total' => (int) $total,
        ]);
    }

    /**
     * export
     *
     * @param  mixed $request
     * @return void
     */
    public function export(Request $request)
    {
        return Excel::download(new SalesExport($request->start_date, $request->end_date), 'sales : ' . $request->start_date . ' - ' . $request->end_date . '.xlsx');
    }

    public function pdf(Request $request)
    {
        //get sales by range date
        $sales = Transaction::with('cashier', 'customer')->whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date)->get();

        //get total sales by range daate
        $total = Transaction::whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date)->sum('grand_total');

        //load view PDF with data
        $pdf = PDF::loadView('exports.sales', compact('sales', 'total'));

        //return PDF for preview / download
        return $pdf->download('sales : ' . $request->start_date . ' — ' . $request->end_date . '.pdf');
    }
}
