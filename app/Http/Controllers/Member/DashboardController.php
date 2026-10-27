<?php

namespace App\Http\Controllers\Member;

use App\Exports\MemberUrlsExport;
use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = ShortUrl::where('user_id', $user->id);

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
                ->editColumn('clicks', function ($data) {
                    return ($data->clicks ?? '0');
                })
                ->editColumn('created_at', function ($data) {
                    return date('d M Y', strtotime($data->created_at));
                })
                ->rawColumns(['short_url'])
                ->make(true);
        }

        return view('member.dashboard');
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $fileName = 'urls_' . date('Y-m-d') . '.csv';

        return Excel::download(new MemberUrlsExport($user->id, $request->interval), $fileName);
    }
}
