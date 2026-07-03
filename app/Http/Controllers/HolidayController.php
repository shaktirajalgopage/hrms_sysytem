<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{


private function routePrefix()
{
    return auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $holidays = Holiday::all();

        return view('admin.holiday.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.holiday.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'description' => 'nullable|string',
        'status' => 'required|boolean',
    ]);

    $validated['no_of_days'] = Carbon::parse($validated['start_date'])
        ->diffInDays(Carbon::parse($validated['end_date'])) + 1;

    Holiday::create($validated);

    return redirect()
        ->route($this->routePrefix() . 'holiday.index')
        ->with('success', 'Holiday created successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        
        // If you don't have a show.blade.php yet, you can redirect back or design one
        return view('admin.holiday.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        
        // Fixed folder path mapping to match your folder structure
        return view('admin.holiday.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $holiday = Holiday::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'description' => 'nullable|string',
        'status' => 'required|boolean',
    ]);

    $validated['no_of_days'] = Carbon::parse($validated['start_date'])
        ->diffInDays(Carbon::parse($validated['end_date'])) + 1;

    $holiday->update($validated);

    return redirect()
        ->route($this->routePrefix() . 'holiday.index')
        ->with('success', 'Holiday updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->route($this->routePrefix() . 'holiday.index')->with('success', 'Holiday deleted successfully.');
    }

    /**
     * Display holiday details for the logged-in employee.
     */
  /**
     * Display all active holiday details for the logged-in employee.
     */
    public function myHolidays()
    {
        // Fetch all active holidays ordered by the closest upcoming date
        $holidays = Holiday::where('status', 1)
            ->orderBy('start_date', 'asc')
            ->get();

        // If there are absolutely no holidays in the database yet
        if ($holidays->isEmpty()) {
            return redirect()->back()->with('error', 'No holidays scheduled at the moment.');
        }

        // Return the show page with the plural $holidays collection wrapper
        return view('admin.holiday.show', compact('holidays'));
    }
}