<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use App\Models\AffiliateClick;
use App\Models\AffiliateConversion;
use App\Models\Post;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminAffiliateLinkController extends Controller
{
    public function __construct(private AuditLogService $auditLogService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('manageAll', Post::class);

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'program' => $request->query('program'),
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'sort' => $request->query('sort', 'latest'),
        ];

        $sorts = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'clicks' => ['clicks', 'desc'],
            'conversions' => ['conversions', 'desc'],
            'earnings' => ['earnings', 'desc'],
        ];

        [$sortColumn, $sortDirection] = $sorts[$filters['sort']] ?? $sorts['latest'];

        $totalClicks = AffiliateLink::sum('clicks');
        $totalConversions = AffiliateLink::sum('conversions');
        $stats = [
            'total_links' => AffiliateLink::count(),
            'active_links' => AffiliateLink::active()->count(),
            'total_clicks' => $totalClicks,
            'clicks_7_days' => AffiliateClick::where('clicked_at', '>=', now()->subDays(7))->count(),
            'clicks_30_days' => AffiliateClick::where('clicked_at', '>=', now()->subDays(30))->count(),
            'total_conversions' => $totalConversions,
            'conversions_7_days' => AffiliateConversion::where('converted_at', '>=', now()->subDays(7))->count(),
            'conversions_30_days' => AffiliateConversion::where('converted_at', '>=', now()->subDays(30))->count(),
            'total_earnings' => AffiliateLink::sum('earnings'),
            'average_conversion_rate' => $totalClicks > 0
                ? round(($totalConversions / $totalClicks) * 100, 2)
                : 0,
        ];

        $dailyClicks = collect(range(6, 0))
            ->map(function (int $daysAgo) {
                $date = now()->subDays($daysAgo);

                return [
                    'label' => $date->format('M d'),
                    'count' => AffiliateClick::whereDate('clicked_at', $date->toDateString())->count(),
                ];
            });

        ['programReport' => $programReport, 'linkReport' => $linkReport] = $this->performanceReport();

        $topLinks = AffiliateLink::with('post')
            ->orderByDesc('clicks')
            ->orderByDesc('conversions')
            ->limit(5)
            ->get();

        $links = AffiliateLink::with('post')
            ->when($filters['q'], function ($query, string $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('affiliate_program', 'like', "%{$term}%")
                        ->orWhere('product_id', 'like', "%{$term}%");
                });
            })
            ->when($filters['program'], fn ($query, string $program) => $query->where('affiliate_program', $program))
            ->when($filters['type'], fn ($query, string $type) => $query->where('type', $type))
            ->when($filters['status'] !== null && $filters['status'] !== '', fn ($query) => $query->where('is_active', $filters['status'] === 'active'))
            ->orderBy($sortColumn, $sortDirection)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $programs = AffiliateLink::query()
            ->select('affiliate_program')
            ->distinct()
            ->orderBy('affiliate_program')
            ->pluck('affiliate_program');

        return view('admin.affiliate-links.index', compact(
            'links',
            'stats',
            'topLinks',
            'filters',
            'programs',
            'dailyClicks',
            'programReport',
            'linkReport'
        ));
    }

    public function create()
    {
        $this->authorize('manageAll', Post::class);

        $posts = Post::published()->orderByDesc('published_at')->get();

        return view('admin.affiliate-links.create', compact('posts'));
    }

    public function export()
    {
        $this->authorize('manageAll', Post::class);

        ['programReport' => $programReport, 'linkReport' => $linkReport] = $this->performanceReport();
        $filename = 'affiliate-performance-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($programReport, $linkReport) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['section', 'program', 'link_title', 'link_slug', 'links', 'clicks', 'conversions', 'conversion_rate', 'earnings']);

            foreach ($programReport as $program) {
                fputcsv($output, [
                    'program',
                    $program['program'],
                    '',
                    '',
                    $program['links'],
                    $program['clicks'],
                    $program['conversions'],
                    $program['conversion_rate'],
                    number_format($program['earnings'], 2, '.', ''),
                ]);
            }

            foreach ($linkReport as $link) {
                $clicks = (int) $link->report_clicks;
                $conversions = (int) $link->report_conversions;

                fputcsv($output, [
                    'link',
                    $link->affiliate_program,
                    $link->title,
                    $link->slug,
                    '',
                    $clicks,
                    $conversions,
                    $clicks > 0 ? round(($conversions / $clicks) * 100, 2) : 0,
                    number_format((float) $link->report_earnings, 2, '.', ''),
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function import(Request $request)
    {
        $this->authorize('manageAll', Post::class);

        $data = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $rows = $this->readCsvRows($data['csv_file']->getRealPath());
        $imported = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $prepared = $this->prepareImportRow($row);

            if (! $prepared) {
                $skipped++;
                continue;
            }

            $existing = AffiliateLink::where('slug', $prepared['slug'])->first();
            AffiliateLink::updateOrCreate(
                ['slug' => $prepared['slug']],
                $prepared
            );

            $existing ? $updated++ : $imported++;
        }

        $this->auditLogService->log(
            $request,
            AffiliateLink::class,
            'affiliate.imported',
            [],
            [
                'imported' => $imported,
                'updated' => $updated,
                'skipped' => $skipped,
            ],
            'Imported affiliate links from CSV.'
        );

        return redirect()
            ->route('admin.affiliate-links.index')
            ->with('status', "Đã import {$imported} link mới, cập nhật {$updated} link, bỏ qua {$skipped} dòng.");
    }

    public function store(Request $request)
    {
        $this->authorize('manageAll', Post::class);

        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);
        $data['is_active'] = $request->boolean('is_active');

        AffiliateLink::create($data);

        return redirect()
            ->route('admin.affiliate-links.index')
            ->with('status', 'Đã tạo affiliate link.');
    }

    public function edit(AffiliateLink $affiliateLink)
    {
        $this->authorize('manageAll', Post::class);

        $posts = Post::published()->orderByDesc('published_at')->get();

        return view('admin.affiliate-links.edit', compact('affiliateLink', 'posts'));
    }

    public function update(Request $request, AffiliateLink $affiliateLink)
    {
        $this->authorize('manageAll', Post::class);

        $data = $this->validatedData($request, $affiliateLink);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title'], $affiliateLink);
        $data['is_active'] = $request->boolean('is_active');

        $affiliateLink->update($data);

        return redirect()
            ->route('admin.affiliate-links.index')
            ->with('status', 'Đã cập nhật affiliate link.');
    }

    public function destroy(AffiliateLink $affiliateLink)
    {
        $this->authorize('manageAll', Post::class);

        $affiliateLink->delete();

        return redirect()
            ->route('admin.affiliate-links.index')
            ->with('status', 'Đã xóa affiliate link.');
    }

    public function recordConversion(Request $request, AffiliateLink $affiliateLink)
    {
        $this->authorize('manageAll', Post::class);
        abort_unless($affiliateLink->is_active, 404);

        $affiliateLink->recordConversion([
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
        ]);

        $this->auditLogService->log(
            $request,
            $affiliateLink,
            'affiliate.conversion_recorded',
            [],
            [
                'conversions' => $affiliateLink->fresh()->conversions,
                'earnings' => $affiliateLink->fresh()->earnings,
            ],
            'Recorded demo affiliate conversion.'
        );

        return redirect()
            ->route('admin.affiliate-links.index')
            ->with('status', 'Đã ghi nhận conversion demo.');
    }

    private function validatedData(Request $request, ?AffiliateLink $affiliateLink = null): array
    {
        return $request->validate([
            'post_id' => ['nullable', 'exists:posts,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'url' => ['required', 'url', 'max:2048'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('affiliate_links', 'slug')->ignore($affiliateLink?->id),
            ],
            'affiliate_program' => ['required', 'string', 'max:255'],
            'product_id' => ['nullable', 'string', 'max:255'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'type' => ['required', 'in:product,service,offer'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $value, ?AffiliateLink $affiliateLink = null): string
    {
        $base = Str::slug($value) ?: 'affiliate-link';
        $slug = $base;
        $counter = 2;

        while (AffiliateLink::where('slug', $slug)->when($affiliateLink, fn ($query) => $query->whereKeyNot($affiliateLink->id))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');

        if (! $handle) {
            return [];
        }

        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);

            return [];
        }

        $header = array_map(fn ($value) => Str::snake(trim((string) $value)), $header);
        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $rows[] = array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));
        }

        fclose($handle);

        return $rows;
    }

    private function prepareImportRow(array $row): ?array
    {
        $title = trim((string) ($row['title'] ?? ''));
        $url = trim((string) ($row['url'] ?? ''));
        $program = trim((string) ($row['affiliate_program'] ?? $row['program'] ?? ''));
        $type = trim((string) ($row['type'] ?? 'product')) ?: 'product';

        if ($title === '' || $url === '' || $program === '' || ! filter_var($url, FILTER_VALIDATE_URL) || ! in_array($type, ['product', 'service', 'offer'], true)) {
            return null;
        }

        $postId = filled($row['post_id'] ?? null) ? (int) $row['post_id'] : null;

        if ($postId && ! Post::whereKey($postId)->exists()) {
            return null;
        }

        $slug = Str::slug((string) ($row['slug'] ?? '')) ?: Str::slug($title);

        return [
            'post_id' => $postId,
            'title' => $title,
            'description' => trim((string) ($row['description'] ?? '')) ?: null,
            'url' => $url,
            'slug' => $slug,
            'affiliate_program' => $program,
            'product_id' => trim((string) ($row['product_id'] ?? '')) ?: null,
            'commission_rate' => is_numeric($row['commission_rate'] ?? null) ? (float) $row['commission_rate'] : null,
            'type' => $type,
            'is_active' => $this->booleanFromCsv($row['is_active'] ?? true),
        ];
    }

    private function booleanFromCsv(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on', 'active'], true);
    }

    private function performanceReport(): array
    {
        $reportSince = now()->subDays(30);
        $performanceLinks = AffiliateLink::query()
            ->with('post')
            ->withCount([
                'clickEvents as report_clicks' => fn ($query) => $query->where('clicked_at', '>=', $reportSince),
                'conversionEvents as report_conversions' => fn ($query) => $query->where('converted_at', '>=', $reportSince),
            ])
            ->withSum([
                'conversionEvents as report_earnings' => fn ($query) => $query->where('converted_at', '>=', $reportSince),
            ], 'amount')
            ->get();

        $programReport = $performanceLinks
            ->groupBy('affiliate_program')
            ->map(fn ($links, string $program) => [
                'program' => $program,
                'links' => $links->count(),
                'clicks' => $links->sum('report_clicks'),
                'conversions' => $links->sum('report_conversions'),
                'earnings' => (float) $links->sum('report_earnings'),
                'conversion_rate' => $links->sum('report_clicks') > 0
                    ? round(($links->sum('report_conversions') / $links->sum('report_clicks')) * 100, 2)
                    : 0,
            ])
            ->sortByDesc('clicks')
            ->take(5)
            ->values();

        $linkReport = $performanceLinks
            ->sortByDesc('report_clicks')
            ->take(5)
            ->values();

        return compact('programReport', 'linkReport');
    }
}
