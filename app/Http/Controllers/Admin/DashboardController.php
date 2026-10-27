<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminUrlsExport;
use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = ShortUrl::with('user')->where('company_id', $user->company_id);

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
                    return '<a href="' . $data->short_url . '" target="_blank">' . $data->short_url . '</a>';
                })
                ->editColumn('original_url', function ($data) {
                    return $data->original_url;
                })
                ->addColumn('user', function ($data) {
                    return optional($data->user)->name ?? '—';
                })
                ->editColumn('clicks', function ($data) {
                    return ($data->clicks ?? '0');
                })
                ->editColumn('created_at', function ($data) {
                    return date('d M Y', strtotime($data->created_at));
                })
                ->rawColumns(['short_url'])
                ->make(true);
        }

        $members = User::where('company_id', $user->company_id)
            ->with('shortUrls')
            ->withCount('shortUrls')
            ->latest()
            ->get();

        return view('admin.dashboard', compact('members'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $fileName = 'urls_' . date('Y-m-d') . '.csv';

        return Excel::download(new AdminUrlsExport($user->company_id, $request->interval), $fileName);
    }
}
