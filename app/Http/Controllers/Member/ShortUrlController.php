<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = auth()->user();

            $request->validate([
                'original_url' => ['required', 'url', 'max:2048'],
            ]);

            $code = Str::lower(Str::random(6));
            if (ShortUrl::where('short_code', $code)->exists()) {
                $code = Str::lower(Str::random(6));
            }

            $shortUrl = ShortUrl::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'original_url' => $request->original_url,
                'short_code' => $code,
                'clicks' => 0,
            ]);

            return redirect()->route('member.dashboard')
                ->with('success', 'Short URL created: ' . $shortUrl->short_url);
        }

        return redirect()->back()->with('error', 'Invalid request.');
    }
}
