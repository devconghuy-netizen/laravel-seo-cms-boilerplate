<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;

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

        $affiliateLink->recordClick();

        return view('products.show', ['product' => $affiliateLink]);
    }
}
