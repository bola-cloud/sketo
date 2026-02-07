<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\SalesInstallment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('user')->orderBy('start_time', 'desc')->paginate(15);
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        return view('admin.shifts.index', compact('shifts', 'activeShift'));
    }

    public function create()
    {
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($activeShift) {
            return redirect()->route('shifts.index')->with('error', 'يوجد وردية مفتوحة بالفعل.');
        }
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        Shift::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return redirect()->route('shifts.index')->with('success', 'تم فتح الوردية بنجاح.');
    }

    public function edit(Shift $shift)
    {
        if ($shift->status === 'closed') {
            return redirect()->route('shifts.index')->with('error', 'هذه الوردية مغلقة بالفعل.');
        }

        // Calculate total sales during this shift
        $totalSales = SalesInstallment::whereHas('invoice', function ($q) use ($shift) {
            $q->where('user_id', $shift->user_id);
        })
            ->whereBetween('date_paid', [$shift->start_time, now()])
            ->sum('amount_paid');

        $expectedCash = $shift->starting_cash + $totalSales;

        return view('admin.shifts.edit', compact('shift', 'totalSales', 'expectedCash'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'ending_cash' => 'required|numeric|min:0',
        ]);

        $totalSales = SalesInstallment::whereHas('invoice', function ($q) use ($shift) {
            $q->where('user_id', $shift->user_id);
        })
            ->whereBetween('date_paid', [$shift->start_time, now()])
            ->sum('amount_paid');

        $expectedCash = $shift->starting_cash + $totalSales;

        $shift->update([
            'end_time' => now(),
            'ending_cash' => $request->ending_cash,
            'expected_cash' => $expectedCash,
            'total_sales' => $totalSales,
            'status' => 'closed',
            'notes' => $request->notes,
        ]);

        return redirect()->route('shifts.index')->with('success', 'تم إغلاق الوردية بنجاح.');
    }
}
