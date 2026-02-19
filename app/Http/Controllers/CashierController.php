<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sales;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\SalesInstallment;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CashierController extends Controller
{
    /**
     * Return cart content partial for AJAX updates
     */
    public function cartContent()
    {
        $cart = session('cart', []);
        // Calculate subtotal as in your main view
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        }
        $discount = 0; // Or get from session/request if needed
        $clients = \App\Models\Client::all();
        return view('admin.cashier.partials.cart_content', compact('cart', 'subtotal', 'discount', 'clients'))->render();
    }
    public function addToCart(Request $request)
    {
        $barcode = $request->input('barcode');
        $product = Product::where('barcode', $barcode)->first();
        if (!$product) {
            return redirect()->route('cashier.viewCart')->with('error', 'المنتج غير موجود.');
        }
        $cart = session()->get('cart', []);
        if (!isset($cart[$barcode])) {
            $cart[$barcode] = [
                'name' => $product->name,
                'price' => $product->selling_price,
                'quantity' => 1,
                'product_id' => $product->id,
                'barcode' => $product->barcode,
                'quantity_available' => $product->quantity,
            ];
        } else {
            $cart[$barcode]['quantity'] += 1;
        }
        session()->put('cart', $cart);
        if ($request->ajax()) {
            return $this->viewCart();
        }
        return redirect()->route('cashier.viewCart')->with('success', 'تم إضافة المنتج إلى العربة.');
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

        // Return view for AJAX or redirect for regular requests
        if ($request->ajax()) {
            return $this->viewCart();
        }
        return redirect()->route('cashier.viewCart');
    }



    public function viewCart()
    {
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeShift) {
            return redirect()->route('shifts.index')->with('info', 'يرجى فتح وردية عمل أولاً قبل استخدام الكاشير.');
        }

        $cart = session()->get('cart', []);
        $subtotal = 0;

        // Calculate subtotal
        foreach ($cart as $barcode => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }
        $clients = Client::all();  // Fetch all suppliers to display in the Select2 dropdown
        return view('admin.cashier.cart', compact('cart', 'subtotal', 'clients'));
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
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeShift) {
            return redirect()->route('shifts.index')->with('info', 'يرجى فتح وردية عمل أولاً قبل استخدام الكاشير.');
        }

        $cart = session()->get('cart', []);
        $discount = $request->input('apply_discount_hidden', 0);
        $paidAmount = $request->input('paid_amount');
        $clientId = $request->input('client_id');

        $validator = Validator::make($request->all(), [
            'paid_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('cashier.viewCart')->withErrors($validator)->withInput();
        }

        try {
            $invoice = $this->processCheckoutLogic($cart, $discount, $paidAmount, $clientId);
            session()->forget('cart');
            return redirect()->route('cashier.printInvoice', $invoice->id)->with('success', 'تمت عملية الدفع بنجاح!');
        } catch (\Exception $e) {
            return redirect()->route('cashier.viewCart')->with('error', 'فشل في الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Process offline synced invoice
     */
    public function syncOfflineInvoice(Request $request)
    {
        $uuid = $request->input('uuid');
        $data = $request->input('data');

        // Idempotency check
        if (Invoice::where('invoice_code', $uuid)->exists()) {
            return response()->json(['message' => 'Already synced'], 200);
        }

        try {
            $this->processCheckoutLogic(
                $data['cart'],
                $data['discount'] ?? 0,
                $data['paid_amount'],
                $data['client_id'] ?? null,
                $uuid,
                $data['user_id'] ?? auth()->id()
            );
            return response()->json(['message' => 'Synced successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Core checkout logic shared between web and sync
     */
    protected function processCheckoutLogic($cart, $discount, $paidAmount, $clientId, $invoiceCode = null, $userId = null)
    {
        $subtotal = 0;
        foreach ($cart as $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        $totalAfterDiscount = $subtotal - $discount;
        $userId = $userId ?? auth()->id();

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_code' => $invoiceCode ?? strtoupper(uniqid('INV-')),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAfterDiscount,
                'paid_amount' => 0,
                'change' => $totalAfterDiscount - $paidAmount,
                'user_id' => $userId,
                'client_id' => $clientId,
            ]);

            foreach ($cart as $barcode => $details) {
                $product = Product::where('barcode', $barcode)->first();
                if (!$product || $product->quantity < $details['quantity']) {
                    throw new \Exception("الكمية غير كافية للمنتج: " . ($product->name ?? $barcode));
                }

                $purchaseProducts = \App\Models\PurchaseProduct::where('product_id', $product->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $remainingToSell = $details['quantity'];
                foreach ($purchaseProducts as $purchaseProduct) {
                    if ($remainingToSell <= 0)
                        break;

                    $soldFromThisBatch = DB::table('sales')
                        ->where('purchase_product_id', $purchaseProduct->id)
                        ->sum('quantity');

                    $availableFromThisBatch = $purchaseProduct->quantity - $soldFromThisBatch;

                    if ($availableFromThisBatch > 0) {
                        $quantityToTakeFromThisBatch = min($remainingToSell, $availableFromThisBatch);
                        Sales::create([
                            'product_id' => $product->id,
                            'quantity' => $quantityToTakeFromThisBatch,
                            'total_price' => $details['price'] * $quantityToTakeFromThisBatch,
                            'invoice_id' => $invoice->id,
                            'purchase_product_id' => $purchaseProduct->id,
                        ]);
                        $purchaseProduct->reduceStock($quantityToTakeFromThisBatch);
                        $remainingToSell -= $quantityToTakeFromThisBatch;
                    }
                }
                $product->recalculateProductQuantity();
            }

            SalesInstallment::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $paidAmount,
                'date_paid' => now(),
            ]);

            $totalPaid = SalesInstallment::where('invoice_id', $invoice->id)->sum('amount_paid');
            $invoice->update([
                'paid_amount' => $totalPaid,
                'change' => $invoice->total_amount - $totalPaid,
            ]);

            DB::commit();
            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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
        if ($query === null || $query === '') {
            $products = Product::where('quantity', '>', 0)->get();
        } else {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('barcode', 'LIKE', "%{$query}%")
                ->where('quantity', '>', 0)
                ->get();
        }
        $results = $products->map(function ($product) {
            return [
                'barcode' => $product->barcode,
                'name' => $product->name,
                'price' => $product->selling_price,
                'quantity' => $product->quantity,
            ];
        });
        return response()->json($results);
    }





}
