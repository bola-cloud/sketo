<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\CashierController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/', [App\Http\Controllers\DashboardController::class ,'index'])->name('dashboard');
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::get('products/search', [App\Http\Controllers\ProductController::class, 'show'])->name('get.search');
    Route::post('products/search', [App\Http\Controllers\ProductController::class, 'search'])->name('products.search');
    Route::get('products/{product}/print-barcode', [App\Http\Controllers\ProductController::class, 'printBarcode'])->name('products.printBarcode');
    Route::post('/products/print-selected-barcodes', [App\Http\Controllers\ProductController::class, 'printSelectedBarcodes'])->name('products.printSelectedBarcodes');
    Route::get('/products/print/barcodes/{id}',[App\Http\Controllers\ProductController::class, 'printBarcodes'])->name('products.printBarcodes');

   Route::get('/cashier/search-product', [CashierController::class, 'searchProductByName'])->name('cashier.searchProductByName');

    Route::get('quantity-updates', [App\Http\Controllers\ProductController::class, 'quantityUpdates'])->name('quantity.updates');
    Route::post('cashier/add-to-cart', [App\Http\Controllers\CashierController::class, 'addToCart'])->name('cashier.addToCart');
    Route::post('/cashier/updateCartQuantity', [App\Http\Controllers\CashierController::class, 'updateCartQuantity'])->name('cashier.updateCartQuantity');
    Route::get('cashier/cart', [App\Http\Controllers\CashierController::class, 'viewCart'])->name('cashier.viewCart');
//   Route::post('cashier/search-product-by-name', [App\Http\Controllers\CashierController::class, 'searchProductByName'])->name('cashier.searchProductByName');
    Route::post('cashier/remove-from-cart', [App\Http\Controllers\CashierController::class, 'removeFromCart'])->name('cashier.removeFromCart');
    Route::post('cashier/checkout', [App\Http\Controllers\CashierController::class, 'checkout'])->name('cashier.checkout');
    Route::get('cashier/invoice/{id}', [App\Http\Controllers\CashierController::class, 'printInvoice'])->name('cashier.printInvoice');
    Route::get('invoices', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/search', [App\Http\Controllers\InvoiceController::class, 'search'])->name('invoices.search');
    Route::get('invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/details', [App\Http\Controllers\InvoiceController::class, 'getDetails']);
    Route::post('/invoices/{invoice}/return', [App\Http\Controllers\InvoiceController::class, 'returnProducts'])->name('invoices.returnProducts');
    Route::post('/invoices/{invoice}/addProduct', [App\Http\Controllers\InvoiceController::class, 'addProduct'])->name('invoices.addProduct'); // New route
    Route::delete('/invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchasesController::class, 'index'])->name('index');
        Route::get('/create', [PurchasesController::class, 'create'])->name('create');
        Route::post('/', [PurchasesController::class, 'store'])->name('store');
        Route::get('/{purchase}', [PurchasesController::class, 'show'])->name('show');
    });
    Route::get('/daily-purchases', [App\Http\Controllers\PurchasesController::class, 'dailyPurchases'])->name('purchases.daily');
    Route::get('/reports/daily', [App\Http\Controllers\ReportController::class, 'dailyReport'])->name('reports.daily');
    Route::get('/reports/monthly', [App\Http\Controllers\ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/report/date/range', [App\Http\Controllers\ReportController::class, 'dateRangeReport'])->name('reports.dateRange');
    Route::get('/purchases/{purchase}/transfer-product/{product}', [App\Http\Controllers\PurchasesController::class, 'transferProductForm'])->name('purchases.transferProduct');
    Route::post('/purchases/{purchase}/transfer-product/{product}', [App\Http\Controllers\PurchasesController::class, 'transferProduct'])->name('purchases.transferProduct.store');
    Route::get('/reports/product-transfers', [App\Http\Controllers\PurchasesController::class, 'productTransfersReport'])->name('reports.productTransfers');

    Route::get('admin/role-user', [App\Http\Controllers\RoleUserController::class, 'index'])->name('role_user.index');
    Route::post('admin/role-user/attach', [App\Http\Controllers\RoleUserController::class, 'attachRole'])->name('role_user.attach');
    Route::post('admin/role-user/detach', [App\Http\Controllers\RoleUserController::class, 'detachRole'])->name('role_user.detach'); 
    Route::get('/admin/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/admin/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');  
    Route::put('/invoices/{invoice}/update-payment', [App\Http\Controllers\InvoiceController::class, 'updatePayment'])->name('invoices.updatePayment'); 
    //purchases installments
    Route::get('purchases/installments/create/{purchase}', [App\Http\Controllers\PurchaseInstallmentController::class, 'create'])->name('purchases.installments.create');
    Route::post('purchases/installments/store', [App\Http\Controllers\PurchaseInstallmentController::class, 'store'])->name('purchases.installments.store');
    Route::delete('purchases/installments/{installment}', [App\Http\Controllers\PurchaseInstallmentController::class, 'destroy'])->name('purchases.installments.destroy');
    //sales installments
    Route::get('sales/installments/{invoice}', [App\Http\Controllers\SalesInstallmentController::class, 'indexInstallments'])->name('sales.installments.index');
    Route::post('sales/installments/{invoice}', [App\Http\Controllers\SalesInstallmentController::class, 'storeInstallment'])->name('sales.installments.store');
    Route::get('/treasury', [App\Http\Controllers\TreasuryController::class, 'treasury'])->name('treasury');
    Route::resource('suppliers', App\Http\Controllers\Suppliercontroller::class);
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    Route::put('invoices/{invoice}/update-discount', [App\Http\Controllers\InvoiceController::class, 'updateDiscount'])->name('invoices.updateDiscount');
    Route::delete('/purchases/{purchase}', [App\Http\Controllers\PurchasesController::class, 'destroy'])->name('purchases.destroy');
    // Route to show the edit form
    Route::get('sales/installments/{invoice}/{installment}/edit', [App\Http\Controllers\SalesInstallmentController::class, 'editInstallment'])->name('sales.installments.edit');
    // Route to update the installment
    Route::put('sales/installments/{invoice}/{installment}', [App\Http\Controllers\SalesInstallmentController::class, 'updateInstallment'])->name('sales.installments.update');
});
