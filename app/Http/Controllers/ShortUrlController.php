<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function redirect(string $code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if (!$shortUrl) {
            return redirect('/')->with('error', 'Short URL not found.');
        }

        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->original_url);
    }
}
