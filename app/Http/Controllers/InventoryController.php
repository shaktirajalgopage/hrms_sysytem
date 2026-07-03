<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index()
{
    $inventories = Inventory::latest()->get();

    return view(
        'admin.inventory.index',
        compact('inventories')
    );
}

public function create()
{
    return view(
        'admin.inventory.create'
    );
}

public function store(Request $request)
{
    $request->validate([
        'asset_type' => 'required',
        'message' => 'nullable',
        'status' => 'required',
    ]);

    Inventory::create([
        'asset_type' => $request->asset_type,
        'message' => $request->message,
        'status' => 'Available',
    ]);

    return redirect()
        ->route('inventory.index')
        ->with('success', 'Inventory created successfully');
}
public function edit($id)
{
    $inventory = Inventory::findOrFail($id);

    return view(
        'admin.inventory.edit',
        compact('inventory')
    );
}
public function update(Request $request, $id)
{
    $request->validate([

        'asset_type' => 'required',

        'status' => 'required'

    ]);

    $inventory = Inventory::findOrFail($id);

    $inventory->update([

        'asset_type' => $request->asset_type,

        'message' => $request->message,

        'status' => $request->status

    ]);

    return redirect()
        ->route('inventory.index')
        ->with(
            'success',
            'Inventory Updated Successfully'
        );
}
public function destroy($id)
{
    $inventory = Inventory::findOrFail($id);

    $inventory->delete();

    return redirect()
        ->route('inventory.index')
        ->with(
            'success',
            'Inventory Deleted Successfully'
        );
}
}
