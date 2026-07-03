@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative main-form-card">

            <div class="header-accent-line"></div>
            <div class="card-header p-4 border-0 d-flex align-items-center gap-3 layout-premium-header">
                <div class="rounded-3 bg-white bg-opacity-10 p-2.5 d-inline-flex header-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="text-info animate-pulse">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold tracking-tight text-white m-0 heading-title">Assign Corporate Assets</h4>
                    <p class="text-white-50 small mb-0 opacity-75 sub-heading-desc">Allocate hardware resources and devices
                        to employee profiles seamlessly.</p>
                </div>
            </div>

            <div class="card-body p-4 bg-white">
                <form action="{{ route('employee-assets.store') }}" method="POST">
                    @csrf

                    <div class="mb-4 form-group-container">
                        <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Select
                            Recipient Employee</label>
                        <div class="input-group custom-input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted px-3 custom-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <select name="employee_id"
                                class="form-select form-control-lg border-start-0 ps-2 text-dark custom-select-input"
                                required style="font-size: 0.95rem;">
                                <option value="" disabled selected>Choose an employee...</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider mb-3">
                            Asset Selection & Allocation
                        </label>

                        <div class="row g-3">
                            @foreach ($inventories as $asset)
                                <div class="col-md-6 col-lg-4">
                                    <div
                                        class="card asset-card h-100 border border-light-subtle rounded-3 transition-all style-card-wrapper">
                                        <label
                                            class="card-body p-3.5 m-0 d-flex flex-column justify-content-between cursor-pointer style-label"
                                            for="asset_{{ $asset->id }}">

                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span
                                                        class="fs-3 bg-light rounded-3 p-2.5 d-inline-block line-height-1 icon-container shadow-sm">
                                                        📦
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark card-asset-title">
                                                            {{ $asset->asset_type }}</h6>
                                                        <small class="text-muted d-block card-asset-desc"
                                                            style="font-size: 0.75rem;">ID: #{{ $asset->id }} —
                                                            {{ $asset->message ?? 'No additional description' }}</small>
                                                    </div>
                                                </div>
                                                <div class="form-check m-0 pointer-events-none custom-checkbox-wrapper">
                                                    <input class="form-check-input asset-checkbox custom-check"
                                                        type="checkbox" name="inventories[]" value="{{ $asset->id }}"
                                                        data-asset-type="{{ $asset->asset_type }}"
                                                        id="asset_{{ $asset->id }}">
                                                </div>
                                            </div>

                                            <div class="qty-wrapper overflow-hidden mt-3"
                                                style="max-height: 0; transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); opacity: 0;">
                                                <div class="pt-2 border-top border-light-subtle">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="small fw-bold text-muted dropdown-qty-label">Assign
                                                            Quantity:</span>

                                                        <div class="input-group custom-stepper-group" style="width: 110px;">
                                                            <button type="button"
                                                                class="btn btn-sm btn-stepper btn-minus d-flex align-items-center justify-content-center"
                                                                disabled>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                    height="12" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="3"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <line x1="5" y1="12" x2="19"
                                                                        y2="12"></line>
                                                                </svg>
                                                            </button>
                                                            <input type="number"
                                                                class="form-control form-control-sm text-center fw-bold asset-qty-input custom-qty-field shadow-none font-monospace text-dark"
                                                                name="qty[{{ $asset->id }}]" min="1"
                                                                value="1" readonly disabled>
                                                            <button type="button"
                                                                class="btn btn-sm btn-stepper btn-plus d-flex align-items-center justify-content-center"
                                                                disabled>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                    height="12" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="3"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <line x1="12" y1="5" x2="12"
                                                                        y2="19"></line>
                                                                    <line x1="5" y1="12" x2="19"
                                                                        y2="12"></line>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="enterprise-workspace-wrapper" class="row d-none mb-5">
                        <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                            <div class="card border-0 bg-light bg-opacity-50 p-3 rounded-4 sticky-top border border-light-subtle"
                                style="top: 24px; z-index: 10;">
                                <span
                                    class="text-secondary small fw-bold text-uppercase tracking-wider mb-3 d-block">Allocation
                                    Tree</span>
                                <div id="allocation-sidebar-list" class="d-flex flex-column gap-2">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-9 col-lg-8">
                            <div id="dynamic-asset-details-container" class="d-flex flex-column gap-4">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 form-group-container">
                        <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Internal
                            Handover Notes</label>
                        <textarea name="message" class="form-control rounded-3 custom-textarea" rows="3"
                            placeholder="Add optional internal references, condition notes, or hardware configuration specifications..."></textarea>
                    </div>

                    <div
                        class="d-flex align-items-center justify-content-end gap-3 border-top pt-4 mt-4 footer-action-bar">
                        <button type="button"
                            class="btn btn-link link-secondary px-3 py-2 text-decoration-none fw-semibold tracking-wide border-0 btn-cancel shadow-none">Cancel</button>
                        <button type="submit"
                            class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold d-inline-flex align-items-center gap-2 shadow brand-btn">
                            <span>Save Allocations</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M5 12l5 5L20 7"></path>
                            </svg>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .pointer-events-none {
            pointer-events: none;
        }

        .line-height-1 {
            line-height: 1;
        }

        .main-form-card {
            border: 1px solid #e2e8f0 !important;
            background-color: #ffffff;
        }

        .header-accent-line {
            height: 4px;
            background: linear-gradient(90deg, #2563eb 0%, #06b6d4 100%);
            width: 100%;
        }

        .layout-premium-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        }

        .header-icon-wrapper {
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
        }

        .custom-input-group {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .custom-input-group:focus-within {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }

        .custom-addon {
            border: none !important;
            background-color: #f8fafc !important;
            color: #64748b !important;
        }

        .custom-select-input,
        .custom-textarea {
            border: none !important;
            box-shadow: none !important;
            background-color: #ffffff !important;
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
        }

        .custom-textarea {
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
            transition: all 0.2s ease;
        }

        .custom-textarea:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }

        .style-card-wrapper {
            border: 1px solid #e2e8f0 !important;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .asset-card {
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease;
        }

        .asset-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
            border-color: #94a3b8 !important;
        }

        .icon-container {
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }

        .asset-card.active-selected {
            border-color: #2563eb !important;
            background-color: #f8fafc;
            box-shadow: 0 4px 18px rgba(37, 99, 235, 0.08);
        }

        .asset-card.active-selected .icon-container {
            background-color: #e0f2fe !important;
            border-color: #bae6fd;
        }

        .asset-card.active-selected .card-asset-title {
            color: #2563eb !important;
        }

        .custom-check {
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0.15rem;
            cursor: pointer;
        }

        .custom-stepper-group {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #ffffff;
            overflow: hidden;
            padding: 2px;
        }

        .btn-stepper {
            background-color: #f8fafc;
            border: none;
            color: #64748b;
            width: 28px;
            height: 28px;
            padding: 0;
            border-radius: 4px !important;
            transition: all 0.15s ease;
        }

        .btn-stepper:hover:not(:disabled) {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .btn-stepper:active:not(:disabled) {
            transform: scale(0.92);
        }

        .custom-qty-field {
            border: none !important;
            background-color: transparent !important;
            padding: 0 !important;
            font-size: 0.9rem;
            height: 28px;
        }

        .custom-qty-field::-webkit-outer-spin-button,
        .custom-qty-field::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .custom-qty-field {
            -moz-appearance: textfield;
        }

        .brand-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            padding: 0.7rem 1.5rem;
            transition: all 0.2s ease;
            letter-spacing: 0.01em;
        }

        .brand-btn:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
        }

        .btn-cancel:hover {
            color: #0f172a !important;
        }

        .sidebar-tree-node {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.65rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .sidebar-tree-node.active-node {
            border-color: #bae6fd;
            background-color: #f0f9ff;
            color: #0369a1;
        }

        .dynamic-premium-card {
            border-top: 3px solid #2563eb !important;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .horizontal-item-container-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.25rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .horizontal-item-container-card:focus-within {
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.04);
        }

        .dynamic-field-control {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.55rem 0.75rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background-color: #ffffff;
        }

        .dynamic-field-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .animate-pulse {
            animation: pulse 2.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            const workspaceWrapper = document.getElementById('enterprise-workspace-wrapper');
            const sidebarList = document.getElementById('allocation-sidebar-list');
            const dynamicContainer = document.getElementById('dynamic-asset-details-container');

            const assetIcons = {
                'Laptop': '💻',
                'Desktop': '🖥',
                'Mouse': '🖱',
                'Keyboard': '⌨',
                'Mobile': '📱'
            };

            checkboxes.forEach(checkbox => {
                const card = checkbox.closest('.asset-card');
                const qtyWrapper = card.querySelector('.qty-wrapper');
                const qtyInput = qtyWrapper.querySelector('.asset-qty-input');
                const btnMinus = qtyWrapper.querySelector('.btn-minus');
                const btnPlus = qtyWrapper.querySelector('.btn-plus');

                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        card.classList.add('active-selected');
                        qtyWrapper.style.maxHeight = "100px";
                        qtyWrapper.style.opacity = "1";
                        qtyInput.removeAttribute('disabled');
                        btnMinus.removeAttribute('disabled');
                        btnPlus.removeAttribute('disabled');
                        updateMinusButtonState(qtyInput, btnMinus);
                    } else {
                        card.classList.remove('active-selected');
                        qtyWrapper.style.maxHeight = "0";
                        qtyWrapper.style.opacity = "0";
                        qtyInput.setAttribute('disabled', 'true');
                        btnMinus.setAttribute('disabled', 'true');
                        btnPlus.setAttribute('disabled', 'true');
                    }
                    regenerateFields();
                });

                btnMinus.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    let currentVal = parseInt(qtyInput.value) || 1;
                    if (currentVal > 1) {
                        qtyInput.value = currentVal - 1;
                        updateMinusButtonState(qtyInput, btnMinus);
                        regenerateFields();
                    }
                });

                btnPlus.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    let currentVal = parseInt(qtyInput.value) || 1;
                    qtyInput.value = currentVal + 1;
                    updateMinusButtonState(qtyInput, btnMinus);
                    regenerateFields();
                });
            });

            function updateMinusButtonState(inputEl, minusBtnEl) {
                let value = parseInt(inputEl.value) || 1;
                if (value <= 1) {
                    minusBtnEl.setAttribute('disabled', 'true');
                } else {
                    minusBtnEl.removeAttribute('disabled');
                }
            }

            function regenerateFields() {
                dynamicContainer.innerHTML = '';
                sidebarList.innerHTML = '';

                let activeCount = 0;

                checkboxes.forEach(checkbox => {
                    if (!checkbox.checked) return;
                    activeCount++;

                    // Using inventory record row ID as primary input binding
                    const inventoryId = checkbox.value;
                    const assetType = checkbox.getAttribute('data-asset-type');
                    const card = checkbox.closest('.asset-card');
                    const qty = parseInt(card.querySelector('.asset-qty-input').value) || 1;
                    const icon = assetIcons[assetType] || '📦';

                    // 1. Sidebar Allocation Node
                    const sidebarNode = document.createElement('div');
                    sidebarNode.className = 'sidebar-tree-node active-node';
                    sidebarNode.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <span>${icon}</span>
                    <span>${assetType} ( #${inventoryId} )</span>
                </div>
                <span class="badge bg-primary rounded-pill font-monospace" style="font-size: 0.7rem;">x${qty}</span>
            `;
                    sidebarList.appendChild(sidebarNode);

                    // 2. Horizontal Form Card Container
                    const colElement = document.createElement('div');
                    colElement.className = 'col-12';

                    let htmlContent = `
                <div class="card border-0 rounded-4 overflow-hidden mb-2 dynamic-premium-card">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <h6 class="mb-0 text-dark fw-bold d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                            <span class="text-primary">${icon}</span>
                            <span>${assetType} Allocation Specification portfolio</span>
                        </h6>
                    </div>
                    <div class="card-body bg-light bg-opacity-25 p-4">
                        <div class="row g-3">
            `;

                    for (let i = 0; i < qty; i++) {
                        let gridClass = (assetType === 'Mobile') ? 'col-xl-6 col-12' : 'col-md-6 col-12';

                        htmlContent += `
                    <div class="${gridClass}">
                        <div class="horizontal-item-container-card h-100">
                            <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                <span class="badge bg-dark bg-opacity-10 text-dark font-monospace rounded-2 px-2 py-0.5" style="font-size: 0.7rem;">#${String(i + 1).padStart(2, '0')}</span>
                                <span class="text-secondary fw-bold small text-uppercase tracking-wider" style="font-size: 0.72rem;">Unit Identification</span>
                            </div>
                            <div class="row g-3">
                `;

                        // Comprehensive Dynamic Mapping Conditional Engine
                        if (assetType === 'Desktop') {
                            htmlContent += `
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">CPU Serial Number <span class="text-danger">*</span></label>
                            <input type="text" name="asset_details[${inventoryId}][${i}][cpu_serial_no]" class="form-control dynamic-field-control w-100" placeholder="Enter CPU Serial" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">Monitor Serial Number <span class="text-danger">*</span></label>
                            <input type="text" name="asset_details[${inventoryId}][${i}][monitor_serial_no]" class="form-control dynamic-field-control w-100" placeholder="Enter Monitor Serial" required>
                        </div>
                    `;
                        } else if (assetType === 'Mobile') {
                            htmlContent += `
        <div class="col-md-6 col-12">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">Mobile Brand / PC <span class="text-danger">*</span></label>
            <input type="text" name="asset_details[${inventoryId}][${i}][brand]" class="form-control dynamic-field-control w-100" placeholder="e.g. Vivo" required>
        </div>
        <div class="col-md-6 col-12">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">Mobile Model <span class="text-danger">*</span></label>
            <input type="text" name="asset_details[${inventoryId}][${i}][model]" class="form-control dynamic-field-control w-100" placeholder="e.g. T4 Lite 5G" required>
        </div>
        <div class="col-md-4 col-12 mt-2">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">Network <span class="text-danger">*</span></label>
            <select name="asset_details[${inventoryId}][${i}][network]" class="form-select dynamic-field-control w-100" required>
                <option value="5G">5G</option>
                <option value="4G">4G / LTE</option>
            </select>
        </div>
        <div class="col-md-4 col-12 mt-2">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">RAM / ROM <span class="text-danger">*</span></label>
            <input type="text" name="asset_details[${inventoryId}][${i}][ram_rom]" class="form-control dynamic-field-control w-100" placeholder="e.g. 8 / 256" required>
        </div>
        <div class="col-md-4 col-12 mt-2">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">Charger Included? <span class="text-danger">*</span></label>
            <select name="asset_details[${inventoryId}][${i}][charger]" class="form-select dynamic-field-control w-100" required>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="col-12 mt-2">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">SIM Number <span class="text-danger">*</span></label>
            <input type="text" name="asset_details[${inventoryId}][${i}][sim_number]" class="form-control dynamic-field-control w-100" placeholder="Enter Contact SIM Number" required>
        </div>
        <div class="col-md-6 col-12 mt-2">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">IMEI 1 <span class="text-danger">*</span></label>
            <input type="text" name="asset_details[${inventoryId}][${i}][imei_1]" class="form-control dynamic-field-control w-100" placeholder="15-digit IMEI 1" required>
        </div>
        <div class="col-md-6 col-12 mt-2">
            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">IMEI 2</label>
            <input type="text" name="asset_details[${inventoryId}][${i}][imei_2]" class="form-control dynamic-field-control w-100" placeholder="15-digit IMEI 2 (Optional)">
        </div>
    `;
                        } else {
                            // Universal Catch-All Fallback Rule (Bag, Headphone, Charger, Tablet, etc.)
                            htmlContent += `
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.78rem;">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" name="asset_details[${inventoryId}][${i}][serial_no]" class="form-control dynamic-field-control w-100" placeholder="Enter ${assetType} Serial Number" required>
                        </div>
                    `;
                        }

                        htmlContent += `</div></div></div>`;
                    }

                    htmlContent += `</div></div></div>`;
                    colElement.innerHTML = htmlContent;
                    dynamicContainer.appendChild(colElement);
                });

                if (activeCount > 0) {
                    workspaceWrapper.classList.remove('d-none');
                } else {
                    workspaceWrapper.classList.add('d-none');
                }
            }
        });
    </script>
@endsection
