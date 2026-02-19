<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Product;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Platform-wide stats (Bypassing VendorScope handled by internal logic or separate queries if needed, 
        // but VendorScope already bypasses for super_admin role)

        $totalVendors = Vendor::count();
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalRevenue = Invoice::sum('paid_amount');

        // Recent Vendors
        $recentVendors = Vendor::with('owner')->latest()->take(5)->get();

        // Revenue by Vendor (Top 5)
        $topVendors = Vendor::withSum('invoices', 'paid_amount')
            ->orderByDesc('invoices_sum_paid_amount')
            ->take(5)
            ->get();

        return view('super_admin.dashboard', compact(
            'totalVendors',
            'totalUsers',
            'totalProducts',
            'totalRevenue',
            'recentVendors',
            'topVendors'
        ));
    }
}
