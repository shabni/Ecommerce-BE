<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Size;
use App\Models\Item;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getSizesForColor($colorId)
    {
        $color = Color::with('size')
            ->whereHas('quantities', function ($query) {
                $query->where('quantity', '>', 0); // Only sizes with available quantity
            })
            ->findOrFail($colorId);

        return response()->json([
            'size' => [
                'id' => $color->size->id,
                'size_name' => $color->size->size_name,
                'available_quantity' => $color->size->quantity,
            ]
        ]);
    }

    public function getColorsForSize($sizeId)
    {
        $size = Size::with(['colors' => function ($query) {
            $query->whereHas('quantities', function ($q) {
                $q->where('quantity', '>', 0);
            });
        }])->findOrFail($sizeId);

        $colors = $size->colors->map(function ($color) {
            return [
                'id' => $color->id,
                'color_name' => $color->color_name,
                'color_code' => $color->color_code,
                'available_quantity' => $color->quantities->quantity,
            ];
        });

        return response()->json($colors);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'disclaimer' => 'nullable|string',
            'instruction' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'sizes' => 'required|array',
            'sizes.*.size_name' => 'required|string|max:255',
            'sizes.*.quantity' => 'required|integer|min:0',
            'sizes.*.colors' => 'required|array',
            'sizes.*.colors.*.color_name' => 'required|string|max:255',
            'sizes.*.colors.*.color_code' => 'nullable|string|max:7',
            'sizes.*.colors.*.quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*.image_url' => 'required|string|max:255',
            'images.*.alt_text' => 'nullable|string|max:255',
        ]);

        // Create the Item
        $item = Item::create($validatedData);

        // Attach sizes and colors
        foreach ($validatedData['sizes'] as $sizeData) {
            $size = $item->sizes()->create([
                'size_name' => $sizeData['size_name'],
                'quantity' => $sizeData['quantity'],
            ]);

            foreach ($sizeData['colors'] as $colorData) {
                $color = $size->colors()->create([
                    'color_name' => $colorData['color_name'],
                    'color_code' => $colorData['color_code'] ?? null,
                ]);

                $color->quantities()->create([
                    'quantity' => $colorData['quantity'],
                ]);
            }
        }

        // Attach images if provided
        if (isset($validatedData['images'])) {
            $item->images()->createMany($validatedData['images']);
        }

        return response()->json([
            'message' => 'Item created successfully.',
            'item' => $item->load('sizes.colors.quantities', 'images'),
        ], 201);
    }

    public function index()
{
    $items = Item::with([
        'sizes.colors.quantities',
        'images'
    ])->get();

    return response()->json($items, 200);
}
}
