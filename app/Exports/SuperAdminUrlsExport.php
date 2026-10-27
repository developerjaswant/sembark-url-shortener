<?php

namespace App\Exports;

use App\Models\ShortUrl;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuperAdminUrlsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $interval;

    public function __construct(?string $interval = null)
    {
        $this->interval = $interval;
    }

    public function collection()
    {
        $query = ShortUrl::with(['company', 'user']);

        $query->when($this->interval === 'today', function ($q) {
            $q->whereDate('created_at', today());
        })->when($this->interval === 'last_week', function ($q) {
            $q->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
        })->when($this->interval === 'this_month', function ($q) {
            $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        })->when($this->interval === 'last_month', function ($q) {
            $q->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
        });

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Short URL',
            'Original URL',
            'Company',
            'Created By',
            'Clicks',
            'Created At',
        ];
    }

    public function map($url): array
    {
        return [
            $url->short_url,
            $url->original_url,
            $url->company?->name ?? '—',
            $url->user?->name ?? '—',
            $url->clicks,
            $url->created_at ? date("Y-m-d", strtotime($url->created_at)) : '',
        ];
    }
}
