<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('owner')->latest()->get();
        return view('super_admin.vendors.index', compact('vendors'));
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
}
