<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublicPlanController extends Controller
{
    public function index()
    {
        $plans = Cache::remember('public_plans', now()->addMinutes(5), function () {
            return Product::query()
                ->with(['nodes:id,name,description'])
                ->where('disabled', false)
                ->where('is_public', true)
                ->orderBy('name')
                ->get()
                ->map(function (Product $product) {
                    return [
                        'id' => $product->id,
                        'slug' => Str::slug($product->name),
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $this->convertToCredits($product->price),
                        'price_display' => $product->display_price,
                        'billing_period' => $product->billing_period,
                        'memory_mb' => (int)$product->memory,
                        'memory_increment_mb' => (int)$product->memory_increment_mb,
                        'memory_increment_price' => $this->convertToCredits($product->memory_increment_price),
                        'memory_increment_max_steps' => (int)$product->memory_increment_max_steps,
                        'player_slots' => (int)$product->player_slots,
                        'slot_increment_step' => (int)$product->slot_increment_step,
                        'slot_increment_price' => $this->convertToCredits($product->slot_increment_price),
                        'slot_increment_max_steps' => (int)$product->slot_increment_max_steps,
                        'base_ram_gb' => round(($product->memory ?? 0) / 1024, 2),
                        'locations' => $product->nodes->map(function ($node) {
                            return [
                                'id' => (string)$node->id,
                                'name' => $node->name,
                                'description' => $node->description,
                            ];
                        })->values(),
                    ];
                })->values();
        });

        return response()->json($plans)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }

    private function convertToCredits(?int $amount): int
    {
        if (is_null($amount)) {
            return 0;
        }

        return (int) round($amount / 1000);
    }
}

