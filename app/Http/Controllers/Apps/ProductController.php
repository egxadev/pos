<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        // GET PRODUCTS
        $products = Product::when(request()->q, function ($products) {
            $products = $products->where('title', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        // RETURN VIEW
        return Inertia::render('Apps/Products/Index', ['products' => $products]);
    }

    public function create()
    {
        // GET CATEGORIES
        $categories = Category::all();

        // RETURN VIEW
        return Inertia::render('Apps/Products/Create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        // VALIDATE
        $this->validate($request, [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'barcode'       => 'required|unique:products',
            'title'         => 'required',
            'description'   => 'required',
            'category_id'   => 'required',
            'buy_price'     => 'required',
            'sell_price'    => 'required',
            'stock'         => 'required',
        ]);

        // UPLOAD IMAGE
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        // CREATE RECORD PRODUCT
        Product::create([
            'image'         => $image->hashName(),
            'barcode'       => $request->barcode,
            'title'         => $request->title,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'buy_price'     => $request->buy_price,
            'sell_price'    => $request->sell_price,
            'stock'         => $request->stock,
        ]);

        // REDIRECT
        return redirect()->route('apps.products.index');
    }

    public function edit(Product $product)
    {
        // GET CATEGORIES
        $categories = Category::all();

        // RETURN VIEW
        return Inertia::render('Apps/Products/Edit', [
            'product'       => $product,
            'categories'    => $categories
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // VALIDATE
        $this->validate($request, [
            'barcode'       => 'required|unique:products,barcode,' . $product->id,
            'title'         => 'required',
            'description'   => 'required',
            'category_id'   => 'required',
            'buy_price'     => 'required',
            'sell_price'    => 'required',
            'stock'         => 'required',
        ]);

        // CHECK IMAGE
        if ($request->file('image')) {
            // REMOVE OLD IMAGE
            Storage::disk('local')->delete('public/products/' . basename($product->image));

            // UPLOAD NEW IMAGE
            $image = $request->file('image');
            $image->storeAs('public/products/', $image->hashName());

            // UPDATE PRODUCT RECORD WITH IMAGE
            $product->update([
                'image' => $image->hashName(),
                'barcode'       => $request->barcode,
                'title'         => $request->title,
                'description'   => $request->description,
                'category_id'   => $request->category_id,
                'buy_price'     => $request->buy_price,
                'sell_price'    => $request->sell_price,
                'stock'         => $request->stock,
            ]);
        }

        // UPDATE PRODUCT RECORD WITHOUT IMAGE
        $product->update([
            'barcode'       => $request->barcode,
            'title'         => $request->title,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'buy_price'     => $request->buy_price,
            'sell_price'    => $request->sell_price,
            'stock'         => $request->stock,
        ]);

        // REDIRECT
        return redirect()->route('apps.products.index');
    }

    public function destroy($id)
    {
        // FIND BY ID
        $product = Product::findOrFail($id);

        // REMOVE IMAGE
        Storage::disk('local')->delete('public/products/' . basename($product->image));

        // DELETE RECORD
        $product->delete();

        // REDIRECT
        return redirect()->route('apps.products.index');
    }
}
