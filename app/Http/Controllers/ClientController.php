<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Display the specified client and their invoices.
     */
    public function show(Client $client)
    {
        // Load the client with their invoices
        $client->load('invoices');
    
        // Calculate total invoices amount, total paid amount, and total change
        $totalInvoices = $client->invoices->sum('total_amount');
        $totalPaidAmount = $client->invoices->sum('paid_amount');
        $totalChange = $client->invoices->sum('change');
    
        return view('admin.clients.show', compact('client', 'totalInvoices', 'totalPaidAmount', 'totalChange'));
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        Client::create($validated);

        return response()->json(['success' => true, 'message' => 'Client created successfully']);
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        return response()->json($client);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $client->update($validated);

        return response()->json(['success' => true, 'message' => 'Client updated successfully']);
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'تم حذف العميل بنجاح.');
    }
}
