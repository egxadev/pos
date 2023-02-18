<?php

namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Profit;
use Illuminate\Http\Request;
use App\Exports\ProfitsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ProfitController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return Inertia::render('Apps/Profits/Index');
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

        // GET DATA PROFITS BY RANGE DATE
        $profits = Profit::with('transaction')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->get();

        // / GET TOTAL PROFIT BY RANGE DATE
        $total = Profit::whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->sum('total');

        // RETURN VIEW
        return Inertia::render('Apps/Profits/Index', [
            'profits'   => $profits,
            'total'     => (int) $total,
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
        return Excel::download(new ProfitsExport($request->start_date, $request->end_date), 'profits : ' . $request->start_date . ' - ' . $request->end_date . '.xlsx');
    }

    /**
     * pdf
     *
     * @param  mixed $request
     * @return void
     */
    public function pdf(Request $request)
    {
        // GET DATA PROFITS BY RANGE
        $profits = Profit::with('transaction')->whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date)->get();

        // GET TOTAL PROFITS BY RANGE
        $total = Profit::whereDate('created_at', '>=', $request->start_date)->whereDate('created_at', '<=', $request->end_date)->sum('total');

        // LOAD VIEW PDF WITH DATA
        $pdf = PDF::loadView('exports.profits', compact('profits', 'total'));

        // DOWNLOAD PDF
        return $pdf->download('profits : ' . $request->start_date . ' — ' . $request->end_date . '.pdf');
    }
}
