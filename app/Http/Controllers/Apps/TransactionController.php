<?php

namespace App\Http\Controllers\Apps;

use App\Models\Cart;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET CART
        $carts = Cart::with('product')
            ->where('cashier_id', auth()->user()->id)
            ->latest()
            ->get();

        // GET ALL CUSTOMERS
        $customers = Customer::latest()->get();

        // RETURN VIEW
        return Inertia::render('Apps/Transactions/Index', [
            'carts'         => $carts,
            'cart_total'    => $carts->sum('price'),
            'customers'     => $customers
        ]);
    }

    /**
     * searchProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function searchProduct(Request $request)
    {
        // FIND BY BARCODE
        $product = Product::where('barcode', $request->barcode)->first();

        if ($product) {
            return response()->json([
                'success'   => true,
                'data'      => $product,
            ]);
        }

        return response()->json([
            'success'       => false,
            'data'          => null,
        ]);
    }

    /**
     * addToCart
     *
     * @param  mixed $request
     * @return void
     */
    public function addToCart(Request $request)
    {
        // CHECK STOCK PRODUCT
        if (Product::whereId($request->product_id)->first()->stock < $request->qty) {
            // REDIRECT
            return redirect()->back()->with(['error', 'Out of Stock Product!']);
        }

        // CHECK CART
        $cart = Cart::with('product')
            ->where('product_id', $request->product_id)
            ->where('cashier_id', $request->cashier_id)
            ->first();

        if ($cart) {
            // INCREMENT QTY
            $cart->increment('qty', $request->qty);

            // SUM PRICE * QTY
            $cart->price = $cart->product->sell_price * $request->qty;

            $cart->save();
        } else {
            // INSERT CART
            Cart::create([
                'cashier_id'        => auth()->user()->id,
                'product_id'        => $request->product_id,
                'qty'               => $request->qty,
                'price'             => $request->sell_price * $request->qty,
            ]);
        }

        // RETURN REDIRECT
        return redirect()->route('apps.transaction.index')->with('success', 'Product Added Successfully!.');
    }

    /**
     * destroyCart
     *
     * @param  mixed $request
     * @return void
     */
    public function destroyCart(Request $request)
    {
        // FIND CART BY ID
        $cart = Cart::with('product')
            ->whereId($request->cart_id)
            ->first();

        // DELETE CART
        $cart->delete();

        // RETURN REDIRECT
        return redirect()->back()->with('success', 'Product Removed Successfully!.');
    }

    public function store(Request $request)
    {
        // GENERATE INVOICE NUMBER
        $length = 10;
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }
        // INVOICE NUMBER
        $invoice = 'TRX-' . Str::upper($random);

        // INSERT TRANSACTION
        $transaction = Transaction::create([
            'cashier_id'    => auth()->user()->id,
            'customer_id'   => $request->customer_id,
            'invoice'       => $invoice,
            'cash'          => $request->cash,
            'change'        => $request->change,
            'discount'      => $request->discount,
            'grand_total'   => $request->grand_total,
        ]);

        // GET CARTS
        $carts = Cart::where('cashier_id', auth()->user()->id)->get();

        // INSERT TRANSACTION DETAIL
        foreach ($carts as $cart) {

            //insert transaction detail
            $transaction->details()->create([
                'transaction_id'    => $transaction->id,
                'product_id'        => $cart->product_id,
                'qty'               => $cart->qty,
                'price'             => $cart->price,
            ]);

            //get price
            $total_buy_price  = $cart->product->buy_price * $cart->qty;
            $total_sell_price = $cart->product->sell_price * $cart->qty;

            //get profits
            $profits = $total_sell_price - $total_buy_price;

            //insert provits
            $transaction->profits()->create([
                'transaction_id'    => $transaction->id,
                'total'             => $profits,
            ]);

            //update stock product
            $product = Product::find($cart->product_id);
            $product->stock = $product->stock - $cart->qty;
            $product->save();
        }

        //delete carts
        Cart::where('cashier_id', auth()->user()->id)->delete();

        return response()->json([
            'success' => true,
            'data'    => $transaction
        ]);
    }

    /**
     * print
     *
     * @param  mixed $request
     * @return void
     */
    public function print(Request $request)
    {
        //get transaction
        $transaction = Transaction::with('details.product', 'cashier', 'customer')->where('invoice', $request->invoice)->firstOrFail();

        //return view
        return view('print.nota', compact('transaction'));
    }
}
