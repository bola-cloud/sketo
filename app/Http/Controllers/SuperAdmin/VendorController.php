<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('owner')->latest()->get();
        return view('super_admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('super_admin.vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $vendor = Vendor::create([
                'owner_id' => $user->id,
                'business_name' => $request->business_name,
                'status' => 'active',
            ]);

            $user->update(['vendor_id' => $vendor->id]);

            // Assign Owner Role
            $user->attachRole('owner');
        });

        return redirect()->route('super-admin.vendors.index')->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        return view('super_admin.vendors.show', compact('vendor'));
    }

    public function updateSubscription(Request $request, Vendor $vendor)
    {
        $request->validate([
            'subscription_ends_at' => 'required|date',
        ]);

        $vendor->update([
            'subscription_ends_at' => $request->subscription_ends_at,
        ]);

        return back()->with('success', 'Subscription updated successfully.');
    }

    public function toggleStatus(Vendor $vendor)
    {
        $newStatus = $vendor->status === 'active' ? 'suspended' : 'active';
        $vendor->update(['status' => $newStatus]);

        return back()->with('success', "Vendor status changed to {$newStatus}.");
    }

    public function search(Request $request)
    {
        $term = $request->input('q');
        $vendors = Vendor::with('owner')
            ->where('business_name', 'LIKE', "%{$term}%")
            ->orWhereHas('owner', function ($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%");
            })
            ->get();

        return response()->json($vendors);
    }

    public function insights(Request $request, Vendor $vendor)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Query vendor-specific data bypassing the global scope if necessary 
        // (but super_admin bypasses VendorScope automatically)

        $totalSales = $vendor->invoices()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('paid_amount');

        $totalProducts = $vendor->products()->count();

        $recentSales = $vendor->invoices()
            ->with('client')
            ->latest()
            ->take(10)
            ->get();

        $topProducts = $vendor->products()
            ->withSum('sales', 'quantity')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get();

        return view('super_admin.vendors.insights', compact(
            'vendor',
            'totalSales',
            'totalProducts',
            'recentSales',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }
}
