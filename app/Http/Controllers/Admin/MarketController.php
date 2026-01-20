<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Inertia\Inertia;
use Inertia\Response;

class MarketController extends Controller
{
    public function show(string $marketId): Response
    {
        $market = Market::with(['category', 'marketProducts.product.unit'])->findOrFail($marketId);
        
        return Inertia::render('admin/markets/show', [
            'market' => $market,
        ]);
    }
}
