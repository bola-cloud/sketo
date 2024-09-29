<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sales;
use App\Models\Invoice;
use App\Models\SalesInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CashierController extends Controller
{
    public function addToCart(Request $request)
    {
        $barcode = $request->input('barcode');
        $product = Product::where('barcode', $barcode)->first();
    
        if (!$product) {
            return redirect()->route('cashier.viewCart')->with('error', 'المنتج غير موجود.');
        }
    
        // Retrieve or initialize the cart from the session
        $cart = session()->get('cart', []);
    
        // Check if the product already exists in the cart
        if (!isset($cart[$barcode])) {
            // If the product is not in the cart, add it with quantity 1
            $cart[$barcode] = [
                'name' => $product->name,
                'price' => $product->selling_price,
                'quantity' => 1,
            ];
    
            // Save the cart back to the session
            session()->put('cart', $cart);
    
            // Optionally return a success message
            return redirect()->route('cashier.viewCart')->with('success', 'تم إضافة المنتج إلى العربة.');
        }
    
        // If the product already exists in the cart, do nothing and return the cart view
        return redirect()->route('cashier.viewCart')->with('info', 'المنتج موجود بالفعل في العربة.');
    }
    
    
    
    public function updateCartQuantity(Request $request)
    {
        $barcode = $request->input('barcode');
        $quantityChange = $request->input('quantity_change');
    
        // Retrieve or initialize the cart from the session
        $cart = session()->get('cart', []);
    
        // Check if the product exists in the cart
        if (isset($cart[$barcode])) {
            $newQuantity = $cart[$barcode]['quantity'] + $quantityChange;
    
            if ($newQuantity > 0) {
                $cart[$barcode]['quantity'] = $newQuantity;
            } else {
                // Remove the item from the cart if the quantity is zero or less
                unset($cart[$barcode]);
            }
    
            // Save the updated cart back to the session
            session()->put('cart', $cart);
        }
    
        return redirect()->route('cashier.viewCart');
    }
    
    

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;

        // Calculate subtotal
        foreach ($cart as $barcode => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        return view('admin.cashier.cart', compact('cart', 'subtotal'));
    }

    public function removeFromCart(Request $request)
    {
        $barcode = $request->input('barcode');
        $cart = session()->get('cart', []);

        if (isset($cart[$barcode])) {
            unset($cart[$barcode]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cashier.viewCart')->with('success', 'تم إزالة المنتج من العربة.');
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
    
        foreach ($cart as $barcode => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }
    
        // Retrieve the discount from the request
        $discount = $request->input('apply_discount_hidden', 0);
        $totalAfterDiscount = $subtotal - $discount;
    
        // Custom validation to ensure paid_amount <= totalAfterDiscount
        $validator = Validator::make($request->all(), [
            'buyer_name' => 'nullable|string|max:255',
            'buyer_phone' => 'nullable|string|max:15',
            'apply_discount_hidden' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ], [
            'paid_amount.required' => 'يرجى إدخال المبلغ المدفوع.',
            'paid_amount.numeric' => 'المبلغ المدفوع يجب أن يكون رقماً.',
            'paid_amount.min' => 'المبلغ المدفوع يجب أن يكون على الأقل 0.',
        ]);
    
        // Add the custom validation for paid_amount
        $validator->after(function ($validator) use ($totalAfterDiscount, $request) {
            if ($request->input('paid_amount') > $totalAfterDiscount) {
                $validator->errors()->add('paid_amount', 'المبلغ المدفوع لا يمكن أن يكون أكبر من الإجمالي بعد الخصم.');
            }
        });
    
        // Check validation
        if ($validator->fails()) {
            return redirect()->route('cashier.viewCart')
                             ->withErrors($validator)
                             ->withInput();
        }
    
        DB::beginTransaction();
    
        try {
            $paidAmount = $request->input('paid_amount');
            $change = $totalAfterDiscount - $paidAmount;
    
            // Create the invoice
            $invoice = Invoice::create([
                'buyer_name' => $request->input('buyer_name'),
                'buyer_phone' => $request->input('buyer_phone'),
                'invoice_code' => strtoupper(uniqid('INV-')),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAfterDiscount,
                'paid_amount' => 0,
                'change' => $change, // Positive if they owe, negative if they overpaid
                'user_id' => auth()->id(),
            ]);
    
            // Process each item in the cart
            foreach ($cart as $barcode => $details) {
                $product = Product::where('barcode', $barcode)->first();
                if ($product->quantity < $details['quantity']) {
                    DB::rollBack();
                    throw ValidationException::withMessages([
                        'cart' => "الكمية المتاحة غير كافية للمنتج: {$product->name}",
                    ]);
                }
                $product->decrement('quantity', $details['quantity']);
                Sales::create([
                    'product_id' => $product->id,
                    'quantity' => $details['quantity'],
                    'total_price' => $details['price'] * $details['quantity'],
                    'invoice_id' => $invoice->id,
                ]);
            }
    
            // Create an installment for the initial payment
            SalesInstallment::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $paidAmount,
                'date_paid' => now(),
            ]);
    
            // Calculate the total paid amount from all installments
            $totalPaid = SalesInstallment::where('invoice_id', $invoice->id)->sum('amount_paid');
    
            // Update the paid_amount in the invoice and recalculate the change
            $invoice->update([
                'paid_amount' => $totalPaid,
                'change' => $invoice->total_amount - $totalPaid,
            ]);
    
            DB::commit();
            session()->forget('cart');
            return redirect()->route('cashier.printInvoice', $invoice->id)->with('success', 'تمت عملية الدفع بنجاح!');
    
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('cashier.viewCart')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cashier.viewCart')->with('error', 'فشل في الدفع: ' . $e->getMessage());
        }
    }
    

    private function calculateDiscount($subtotal)
    {
        if ($subtotal >= 6000) {
            return 500;
        } elseif ($subtotal >= 5000) {
            return 400;
        } elseif ($subtotal >= 4000) {
            return 300;
        } elseif ($subtotal >= 3000) {
            return 200;
        } else {
            return 0; // No discount for totals below 3000
        }
    }
    
    public function printInvoice($id)
    {
 
        $invoice = Invoice::with('sales.product')->findOrFail($id);
        return view('admin.cashier.invoice', compact('invoice'));
    }

    public function searchProductByName(Request $request)
    {
        $query = $request->input('query');
    
        if (!$query) {
            return response()->json([]); // Return an empty response if no query is provided
        }
    
        // Search for products and return both the 'name' and 'barcode' fields
        $products = Product::where('name', 'LIKE', "%{$query}%")->get(['name', 'barcode']);
    
        return response()->json($products); // Return product names and barcodes as JSON
    }
    

    
    

}
