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
                ->with(['nodes:id,location_id', 'nodes.location:id,name,description'])
                ->where('disabled', false)
                ->where('is_public', true)
                ->orderBy('name')
                ->get()
                ->map(function (Product $product) {
                    $priceCredits = $this->convertToCredits($product->price);
                    $memoryIncrementCredits = $this->convertToCredits($product->memory_increment_price);
                    $slotIncrementCredits = $this->convertToCredits($product->slot_increment_price);

                    $locations = $product->nodes
                        ->filter(fn ($node) => $node->location)
                        ->map(function ($node) {
                            return [
                                'id' => (string) $node->location->id,
                                'name' => $node->location->name ?? 'Location ' . $node->location->id,
                                'description' => $node->location->description,
                            ];
                        })
                        ->unique('id')
                        ->values();

                    return [
                        'id' => $product->id,
                        'slug' => Str::slug($product->name),
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $priceCredits,
                        'price_aud' => $this->convertCreditsToAud($priceCredits),
                        'price_display' => $product->display_price,
                        'billing_period' => $product->billing_period,
                        'memory_mb' => (int)$product->memory,
                        'memory_increment_mb' => (int)$product->memory_increment_mb,
                        'memory_increment_price' => $memoryIncrementCredits,
                        'memory_increment_price_aud' => $this->convertCreditsToAud($memoryIncrementCredits),
                        'memory_increment_max_steps' => (int)$product->memory_increment_max_steps,
                        'player_slots' => (int)$product->player_slots,
                        'slot_increment_step' => (int)$product->slot_increment_step,
                        'slot_increment_price' => $slotIncrementCredits,
                        'slot_increment_price_aud' => $this->convertCreditsToAud($slotIncrementCredits),
                        'slot_increment_max_steps' => (int)$product->slot_increment_max_steps,
                        'base_ram_gb' => round(($product->memory ?? 0) / 1024, 2),
                        'locations' => $locations,
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

    private function convertCreditsToAud(int $credits): float
    {
        return round($credits / 100, 2);
    }
}

