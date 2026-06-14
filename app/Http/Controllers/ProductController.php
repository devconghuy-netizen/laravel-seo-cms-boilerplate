<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = AffiliateLink::active()
            ->latest()
            ->paginate(12);

        return view('products.index', compact('products'));
    }

    public function show(AffiliateLink $affiliateLink)
    {
        abort_unless($affiliateLink->is_active, 404);

        return view('products.show', ['product' => $affiliateLink]);
    }

    public function redirect(Request $request, AffiliateLink $affiliateLink)
    {
        abort_unless($affiliateLink->is_active, 404);

        $affiliateLink->recordClick([
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
        ]);

        return redirect()->away($affiliateLink->url);
    }
}
