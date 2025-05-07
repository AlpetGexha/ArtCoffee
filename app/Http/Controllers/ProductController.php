<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Menu;
use App\Enum\ProductCategory;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display the product catalog page
     */
    public function index(): View
    {
        $products = Product::query()
            ->where('is_available', true)
            ->with('media')
            ->get();

        $menus = Menu::query()
            ->where('is_active', true)
            ->get();

        $categories = collect(ProductCategory::cases())
            ->map(fn (ProductCategory $category) => [
                'value' => $category->value,
                'name' => $category->name,
            ]);

        return view('products.index', compact('products', 'menus', 'categories'));
    }

    /**
     * Display the details for a specific product
     */
    public function show(Product $product): View
    {
        $product->load('media');

        $relatedProducts = Product::query()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->with('media')
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
