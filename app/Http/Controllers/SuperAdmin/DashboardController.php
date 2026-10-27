<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exports\SuperAdminUrlsExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = ShortUrl::with(['company', 'user']);

        $query->when($request->interval === 'today', function ($q) {
            $q->whereDate('created_at', today());
        })->when($request->interval === 'last_week', function ($q) {
            $q->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
        })->when($request->interval === 'this_month', function ($q) {
            $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        })->when($request->interval === 'last_month', function ($q) {
            $q->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
        });

        if ($request->ajax()) {
            return DataTables::of($query)
                ->editColumn('short_url', function ($data) {
                    return '<a href="' . $data->short_url . '" target="_blank" >' . $data->short_url . '</a>';
                })
                ->editColumn('original_url', function ($data) {
                    return $data->original_url;
                })
                ->addColumn('company', function ($data) {
                    return optional($data->company)->name ?? '—';
                })
                ->addColumn('user', function ($data) {
                    return optional($data->user)->name ?? '—';
                })
                ->editColumn('clicks', function ($data) {
                    return ($data->clicks ?? '—');
                })
                ->editColumn('created_at', function ($data) {
                    return date('d M Y', strtotime($data->created_at));
                })
                ->rawColumns(['short_url', 'original_url', 'company', 'clicks'])
                ->make(true);
        }

        $companies = Company::withCount(['users', 'shortUrls'])->latest()->get();

        return view('superadmin.dashboard', compact('companies'));
    }

    public function export(Request $request)
    {
        $fileName = 'urls_' . date('Y-m-d') . '.csv';

        return Excel::download(new SuperAdminUrlsExport($request->interval), $fileName);
    }
}
