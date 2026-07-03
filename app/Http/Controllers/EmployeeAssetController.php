<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAsset;
use Illuminate\Http\Request;
use App\Models\AssetRequest;
use App\Models\Inventory;
// FIX 1: Explicitly import the standard core Laravel Response Facade
use Illuminate\Support\Facades\Response;

class EmployeeAssetController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('firstname')->get();

        $employeeAssets = EmployeeAsset::with('employee')
            ->latest()
            ->get();

        return view(
            'admin.employee_assets.index',
            compact('employees', 'employeeAssets')
        );
    }

    public function create()
    {
        $employees = Employee::orderBy('firstname')->get();

        $inventories = Inventory::where('status', 'Available')->get();

        return view(
            'admin.employee_assets.create',
            compact('employees', 'inventories')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'inventories' => 'required|array', 
        ]);

        $assetDetails = [];
        $assetNames = [];

        foreach ($request->inventories as $inventoryId) {
            $inventory = Inventory::where('id', $inventoryId)
                ->where('status', 'Available')
                ->first();

            if ($inventory) {
                $assetNames[] = $inventory->asset_type;
                
                $assetDetails[] = [
                    'inventory_id' => $inventory->id,
                    'asset'        => $inventory->asset_type,
                    'qty'          => (int) ($request->qty[$inventoryId] ?? 1),
                    'items'        => $request->asset_details[$inventoryId] ?? []
                ];

            }
        }

        if (empty($assetDetails)) {
            return redirect()->back()->withErrors(['inventories' => 'No available inventory assets selected.']);
        }

        EmployeeAsset::updateOrCreate(
            [
                'employee_id' => $request->employee_id
            ],
            [
                'asset_name'    => implode(',', array_unique($assetNames)),
                'asset_details' => $assetDetails,
                'message'       => $request->message,
                'assigned_date' => now(),
                'status'        => 'Assigned'
            ]
        );

        return redirect()
            ->route('employee-assets.index')
            ->with('success', 'Asset Assigned Successfully');
    }

    public function edit($id)
    {
        $employeeAsset = EmployeeAsset::findOrFail($id);
        $employees = Employee::orderBy('firstname')->get();
        $inventories = Inventory::all(); 

        return view('admin.employee_assets.edit', compact('employeeAsset', 'employees', 'inventories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required',
            'inventories' => 'required|array', 
            'status'      => 'required|string',
        ]);

        $assetDetails = [];
        $assetNames = [];

        foreach ($request->inventories as $inventoryId) {
            $inventory = Inventory::find($inventoryId);

            if ($inventory) {
                $assetNames[] = $inventory->asset_type;
                
                $rawItems = $request->asset_details[$inventoryId] ?? [];
                $sanitizedItems = [];

                foreach ($rawItems as $item) {
                    $sanitizedItems[] = $item;
                }

                $assetDetails[] = [
                    'inventory_id' => $inventory->id,
                    'asset'        => $inventory->asset_type,
                    'qty'          => (int) ($request->qty[$inventoryId] ?? 1),
                    'items'        => $sanitizedItems
                ];
            }
        }

        $employeeAsset = EmployeeAsset::findOrFail($id);

        $employeeAsset->update([
            'employee_id'   => $request->employee_id,
            'asset_name'    => implode(',', array_unique($assetNames)),
            'asset_details' => $assetDetails,
            'message'       => $request->message,
            'status'        => $request->status,
        ]);

        return redirect()
            ->route('employee-assets.index')
            ->with('success', 'Asset Updated Successfully');
    }

    public function destroy($id)
    {
        $asset = EmployeeAsset::findOrFail($id);
        $asset->delete();

        return redirect()
            ->route('employee-assets.index')
            ->with('success', 'Asset Deleted Successfully');
    }

    public function myAssets()
    {
        $employee = Employee::where('email', auth()->user()->email)->first();

        $assets = EmployeeAsset::where('employee_id', $employee->id)->get();

        return view('employee.assets.index', compact('assets'));
    }

    public function requests($id)
    {
        $asset = EmployeeAsset::with('employee')->findOrFail($id);

        $requests = AssetRequest::where('employee_asset_id', $id)
            ->latest()
            ->get();

        return view(
            'admin.employee_assets.requests',
            compact('asset', 'requests')
        );
    }

    public function assetRequestEdit($id)
    {
        $requestData = AssetRequest::with(['employee', 'asset'])->findOrFail($id);

        return view('admin.employee_assets.asset_requests_edit', compact('requestData'));
    }

    public function assetRequestUpdate(Request $request, $id)
    {
        $assetRequest = AssetRequest::findOrFail($id);

        $assetRequest->update([
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('employee-assets.requests', $assetRequest->employee_asset_id)
            ->with('success', 'Request Updated Successfully');
    }

   public function bulkView()
{
    $assetTypes = Inventory::select('asset_type')
        ->distinct()
        ->orderBy('asset_type')
        ->pluck('asset_type');

    return view(
        'admin.employee_assets.bulk_import',
        compact('assetTypes')
    );
}

   public function downloadTemplate(Request $request)
{
    $assetType = $request->assetType;

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename={$assetType}_template.csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    switch ($assetType) {

        case 'Mobile':

            $columns = [
                'Empid',
                'Asset_Type',
                'MOBILE_PC',
                'MOBILE_MODEL',
                'Network',
                'RAM_ROM',
                'SIM_NUMBER',
                'IMEI_1',
                'IMEI_2',
                'Charger'
            ];

            $sampleData = [
                'AISPL001',
                'Mobile',
                'Vivo',
                'T4 Lite 5G',
                '5G',
                '8 / 256',
                '9348222048',
                '862887072163352',
                '862887072163345',
                'Yes'
            ];

            break;

        case 'Desktop':

            $columns = [
                'Empid',
                'Asset_Type',
                'CPU_SERIAL_NO',
                'MONITOR_SERIAL_NO'
            ];

            $sampleData = [
                'AISPL001',
                'Desktop',
                'CPU123456',
                'MON123456'
            ];

            break;

        default:

            // Laptop, Mouse, Keyboard, Headset, Bag, Monitor etc.
            $columns = [
                'Empid',
                'Asset_Type',
                'SERIAL_NO'
            ];

            $sampleData = [
                'AISPL001',
                $assetType,
                'SN123456'
            ];

            break;
    }

    $callback = function () use ($columns, $sampleData) {

        $file = fopen('php://output', 'w');

        fputcsv($file, $columns);
        fputcsv($file, $sampleData);

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function bulkStore(Request $request)
{
    $request->validate([
        'bulk_file' => 'required|file|mimes:csv,txt'
    ]);

    $file = $request->file('bulk_file');
    $handle = fopen($file->getRealPath(), "r");

    // Remove BOM if present
    $bom = fread($handle, 3);
    if ($bom !== "\xEF\xBB\xBF") {
        rewind($handle);
    }

    // Skip header row
    fgetcsv($handle, 1000, ",");

    $errorsList = [];
    $processedCount = 0;

    while (($row = fgetcsv($handle, 1000, ",")) !== false) {

        if (empty($row) || trim($row[0]) === '') {
            continue;
        }

        $empid     = trim($row[0]);
        $assetType = trim($row[1]);

        // Employee validation
        $employee = Employee::where('unique_id', $empid)->first();

        if (!$employee) {
            $errorsList[] = "Employee code [{$empid}] not found. Row skipped.";
            continue;
        }

        // Asset Type Validation
        $inventory = Inventory::where('asset_type', $assetType)->first();

        if (!$inventory) {
            $errorsList[] = "Asset Type [{$assetType}] not found. Row skipped.";
            continue;
        }

        /**
         * Build payload according to Asset Type
         */
        if ($assetType === 'Mobile') {

            $newItemPayload = [
                'brand'      => trim($row[2] ?? ''),
                'model'      => trim($row[3] ?? ''),
                'network'    => trim($row[4] ?? ''),
                'ram_rom'    => trim($row[5] ?? ''),
                'sim_number' => trim($row[6] ?? ''),
                'imei_1'     => trim($row[7] ?? ''),
                'imei_2'     => trim($row[8] ?? ''),
                'charger'    => trim($row[9] ?? ''),
            ];

        } elseif ($assetType === 'Desktop') {

            $newItemPayload = [
                'cpu_serial_no'     => trim($row[2] ?? ''),
                'monitor_serial_no' => trim($row[3] ?? ''),
            ];

        } else {

            // Laptop, Mouse, Keyboard, Headset, Bag etc.
            $newItemPayload = [
                'serial_no' => trim($row[2] ?? ''),
            ];
        }

        $employeeAsset = EmployeeAsset::where('employee_id', $employee->id)->first();

        if ($employeeAsset) {

            $currentDetails = is_string($employeeAsset->asset_details)
                ? json_decode($employeeAsset->asset_details, true)
                : $employeeAsset->asset_details;

            if (!is_array($currentDetails)) {
                $currentDetails = [];
            }

            $assetFound = false;

            foreach ($currentDetails as &$block) {

                if (
                    isset($block['inventory_id']) &&
                    $block['inventory_id'] == $inventory->id
                ) {

                    if (!isset($block['items']) || !is_array($block['items'])) {
                        $block['items'] = [];
                    }

                    $block['items'][] = $newItemPayload;

                    // Auto maintain qty
                    $block['qty'] = count($block['items']);

                    $assetFound = true;
                    break;
                }
            }

            if (!$assetFound) {

                $currentDetails[] = [
                    'inventory_id' => $inventory->id,
                    'asset'        => $inventory->asset_type,
                    'qty'          => 1,
                    'items'        => [$newItemPayload]
                ];
            }

            $assetNames = array_filter(
                explode(',', $employeeAsset->asset_name ?? '')
            );

            $assetNames[] = $inventory->asset_type;

            $employeeAsset->update([
                'asset_name'    => implode(',', array_unique($assetNames)),
                'asset_details' => $currentDetails,
            ]);

        } else {

            EmployeeAsset::create([
                'employee_id'   => $employee->id,
                'asset_name'    => $inventory->asset_type,
                'asset_details' => [
                    [
                        'inventory_id' => $inventory->id,
                        'asset'        => $inventory->asset_type,
                        'qty'          => 1,
                        'items'        => [$newItemPayload]
                    ]
                ],
                'message'       => 'Imported via Bulk Upload',
                'assigned_date' => now(),
                'status'        => 'Assigned',
            ]);
        }

       

        $processedCount++;
    }

    fclose($handle);

    if ($processedCount === 0 && !empty($errorsList)) {
        return redirect()->back()
            ->withErrors([
                'bulk_file' => 'No records were imported.'
            ])
            ->with('error_rows', $errorsList);
    }

    return redirect()
        ->route('employee-assets.index')
        ->with(
            'success',
            "{$processedCount} assets imported successfully."
        )
        ->with('error_rows', $errorsList);
}
}