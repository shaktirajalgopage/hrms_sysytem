@extends('layouts.admin')

@section('title', 'Bulk Asset Import')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative main-form-card">
        
        <div class="header-accent-line"></div>
        <div class="card-header p-4 border-0 d-flex align-items-center justify-content-between layout-premium-header">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-white bg-opacity-10 p-2.5 d-inline-flex header-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-info"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold tracking-tight text-white m-0 heading-title">Bulk Import Employee Assets via Excel/CSV</h4>
                    <p class="text-white-50 small mb-0 opacity-75 sub-heading-desc">Allocate system assets across multiple global organizational profiles concurrently.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">

    <select id="assetTypeSelect"
            class="form-select form-select-sm"
            style="min-width:220px">
        @foreach($assetTypes as $type)
            <option value="{{ $type }}">
                {{ $type }}
            </option>
        @endforeach
    </select>

    <button type="button"
            id="downloadTemplateBtn"
            class="btn btn-sm btn-light text-primary fw-bold px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">

        <svg xmlns="http://www.w3.org/2000/svg"
             width="16"
             height="16"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>

        <span>Download Template</span>
    </button>

</div>
        </div>

        <div class="card-body p-4 bg-white">
            
            @if(session('error_rows'))
                <div class="alert alert-danger rounded-3 mb-4">
                    <h6 class="fw-bold mb-2">Import completed with structural anomalies:</h6>
                    <ul class="mb-0 small ps-3">
                        @foreach(session('error_rows') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-light bg-opacity-50 border p-4 rounded-4 mb-4">
                <h6 class="fw-bold text-dark small text-uppercase tracking-wider mb-3">Instructions & Requirements:</h6>
                <ul class="small text-secondary mb-0 d-flex flex-column gap-2 ps-3">
                    <li>Download the template spreadsheet tracking system configuration using the top action button.</li>
                    <li>The system correlates rows against existing corporate records via the <strong class="text-dark">Empid</strong> parameter matching the employee's <code class="bg-white border text-primary px-1 rounded">unique_id</code> column.</li>
                    <li>The platform validates asset conditions instantly against the live <strong class="text-dark">Inventory</strong> layer. A target unit is skipped if its item availability status is not marked as <span class="badge bg-success-subtle text-success">Available</span>.</li>
                </ul>
            </div>

            <form action="{{ route('employee-assets.bulk-store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4 form-group-container">
                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Select CSV Allocation Spreadsheet File</label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted px-3 custom-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        </span>
                        <input type="file" name="bulk_file" class="form-control form-control-lg border-start-0 ps-2 text-dark custom-select-input" accept=".csv" required style="font-size: 0.95rem;">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-3 border-top pt-4 mt-4 footer-action-bar">
                    <a href="{{ route('employee-assets.index') }}" class="btn btn-link link-secondary px-3 py-2 text-decoration-none fw-semibold tracking-wide border-0 btn-cancel shadow-none">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold d-inline-flex align-items-center gap-2 shadow brand-btn">
                        <span>Execute Bulk Assignments</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<style>
    .main-form-card { border: 1px solid #e2e8f0 !important; background-color: #ffffff; }
    .header-accent-line { height: 4px; background: linear-gradient(90deg, #2563eb 0%, #06b6d4 100%); width: 100%; }
    .layout-premium-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; }
    .header-icon-wrapper { border: 1px solid rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px); }
    .custom-input-group { border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; transition: all 0.2s ease; }
    .custom-input-group:focus-within { border-color: #2563eb !important; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important; }
    .custom-addon { border: none !important; background-color: #f8fafc !important; color: #64748b !important; }
    .custom-select-input { border: none !important; box-shadow: none !important; background-color: #ffffff !important; padding-top: 0.65rem; padding-bottom: 0.65rem; }
    .brand-btn { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important; border: none !important; padding: 0.7rem 1.5rem; transition: all 0.2s ease; }
    .brand-btn:hover { background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important; }
</style>


<script>
document.getElementById('downloadTemplateBtn')
    .addEventListener('click', function () {

        const assetType =
            document.getElementById('assetTypeSelect').value;

        window.location.href =
            "{{ route('employee-assets.download-template') }}" +
            "?assetType=" +
            encodeURIComponent(assetType);
    });
</script>


@endsection