<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JobPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobPositionController extends Controller
{

  private function routePrefix()
{
    return auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
}

    public function index()
    {
        $positions = JobPosition::withCount('applications')
            ->latest()
            ->paginate(15);

        return view('admin.interviews.positions.index', [
            'positions' => $positions,
            'types'     => JobPosition::types(),
        ]);
    }

    public function create()
    {
        return view('admin.interviews.positions.create', [
            'types' => JobPosition::types(),
        ]);
    }

    public function store(Request $request)
    {

    //dd($request->all());
        $request->validate([
            'title'      => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'location'   => 'nullable|string|max:255',
            'type'       => 'required|in:' . implode(',', array_keys(JobPosition::types())),
            'openings'   => 'required|integer|min:1',
        ]);

        JobPosition::create([
            ...$request->only(['title', 'department', 'location', 'type', 'openings']),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route($this->routePrefix() . 'positions.index')
            ->with('success', 'Job position created.');
    }

    public function edit(JobPosition $position)
    {
        return view('admin.interviews.positions.edit', [

            'position' => $position,
            'types'    => JobPosition::types(),
        ]);
    }

    public function update(Request $request, JobPosition $position)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'location'   => 'nullable|string|max:255',
            'type'       => 'required|in:' . implode(',', array_keys(JobPosition::types())),
            'openings'   => 'required|integer|min:1',
            'is_active'  => 'boolean',
        ]);

        $position->update($request->only(['title', 'department', 'location', 'type', 'openings', 'is_active']));

        return redirect()->route($this->routePrefix() . 'positions.index')
            ->with('success', 'Position updated.');
    }

    public function destroy(JobPosition $position)
    {
        $position->delete();
        return back()->with('success', 'Position deleted.');
    }
}
