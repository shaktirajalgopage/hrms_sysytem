@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager' ? 'hr.' : '';
@endphp

@section('content')
    <div class="lac-root">

        {{-- ── Page Header ─────────────────────────────────────────────── --}}
        <div class="lac-header">
            <div class="lac-header__left">
                <div class="lac-header__eyebrow">
                    <i class="fas fa-layer-group"></i>
                    Leave Management
                </div>
                <h1 class="lac-header__title">Assignment Console</h1>
                <p class="lac-header__subtitle">Bulk assign or sync leave policies onto employee profiles.</p>
            </div>
            <div class="lac-header__right">
                <form action="{{ route($routePrefix . 'employee-leave.assignments.index') }}" method="GET"
                    id="yearPickerForm">
                    <div class="lac-year-picker">
                        <i class="far fa-calendar-alt lac-year-picker__icon"></i>
                        <div class="lac-year-picker__inner">
                            <label for="year" class="lac-year-picker__label">Policy Year</label>
                            <select name="year" id="year" class="lac-year-picker__select"
                                onchange="document.getElementById('yearPickerForm').submit();">
                                @for ($y = date('Y') - 2; $y <= date('Y') + 3; $y++)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <i class="fas fa-chevron-down lac-year-picker__caret"></i>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Flash Messages ───────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="lac-alert lac-alert--success">
                <i class="fas fa-check-circle lac-alert__icon"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="lac-alert lac-alert--error">
                <i class="fas fa-exclamation-circle lac-alert__icon"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route($routePrefix . 'employee-leave.assignments.assign') }}" method="POST">
            @csrf
            <input type="hidden" name="year" value="{{ $selectedYear }}">

            <div class="lac-layout">

                {{-- ── Left: Employee Table ────────────────────────────────── --}}
                <div class="lac-layout__main">
                    <div class="lac-card">

                        {{-- Card Header --}}
                        <div class="lac-card__header">
                            <div class="lac-search">
                                <i class="fas fa-search lac-search__icon"></i>
                                <input type="text" id="datatableSearch" class="lac-search__input"
                                    placeholder="Search name, ID, or department…">
                            </div>
                            <div class="lac-select-all-wrap">
                                <label class="lac-checkbox-label" for="selectAllEmployees">
                                    <input type="checkbox" class="lac-checkbox" id="selectAllEmployees">
                                    <span class="lac-checkbox-label__text">Select all on page</span>
                                </label>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="lac-table-wrap">
                            <table class="lac-table" id="employeeDataTable">
                                <thead>
                                    <tr>
                                        <th class="lac-table__th lac-table__th--check"></th>
                                        <th class="lac-table__th">Employee</th>
                                        <th class="lac-table__th">Department</th>
                                        <th class="lac-table__th">{{ $selectedYear }} Allowances</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $emp)
                                        @php $hasAlloc = $emp->leaveAllocations->isNotEmpty(); @endphp
                                        <tr
                                            class="lac-table__row data-row {{ $hasAlloc ? 'lac-table__row--assigned' : '' }}">
                                            <td class="lac-table__td lac-table__td--check">
                                                <label class="lac-checkbox-label" for="empCheck{{ $emp->id }}">
                                                    <input type="checkbox" name="employee_ids[]"
                                                        value="{{ $emp->id }}" class="lac-checkbox employee-checkbox"
                                                        id="empCheck{{ $emp->id }}">
                                                </label>
                                            </td>

                                            <td class="lac-table__td search-cell-profile">
                                                <div class="lac-employee">
                                                    <div class="lac-avatar"
                                                        data-initials="{{ strtoupper(substr($emp->firstname, 0, 1) . substr($emp->lastname, 0, 1)) }}">
                                                    </div>
                                                    <div class="lac-employee__info">
                                                        <span class="lac-employee__name data-name">{{ $emp->firstname }}
                                                            {{ $emp->lastname }}</span>
                                                        <span
                                                            class="lac-employee__uid data-uid">{{ $emp->unique_id }}</span>
                                                    </div>
                                                    @if ($hasAlloc)
                                                        <span class="lac-badge lac-badge--assigned">Assigned</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="lac-table__td search-cell-dept">
                                                <span
                                                    class="lac-dept data-dept">{{ $emp->department?->title ?? '—' }}</span>
                                            </td>

                                            <td class="lac-table__td">
                                                <div class="lac-alloc-chips">
                                                    @forelse($emp->leaveAllocations as $alloc)
                                                        <span class="lac-chip">
                                                            <span
                                                                class="lac-chip__code">{{ $alloc->leaveType->code }}</span>
                                                            <span
                                                                class="lac-chip__days">{{ $alloc->allocated_days }}d</span>
                                                        </span>
                                                    @empty
                                                        <span class="lac-unassigned">Not assigned</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Footer --}}
                        <div class="lac-card__footer" id="datatablePaginationBar">
                            <span class="lac-pagination__summary" id="paginationSummary">Showing 0 to 0 of 0
                                employees</span>
                            <ul class="lac-pagination" id="paginationControlWrapper"></ul>
                        </div>
                    </div>
                </div>

                {{-- ── Right: Policy Panel ─────────────────────────────────── --}}
                <div class="lac-layout__aside">
                    <div class="lac-panel">
                        <div class="lac-panel__header">
                            <div class="lac-panel__title-row">
                                <i class="fas fa-sliders-h lac-panel__icon"></i>
                                <h6 class="lac-panel__title">Leave Policies</h6>
                            </div>
                            <button type="button" class="lac-link-btn" id="selectAllLeaves">Select all</button>
                        </div>

                        <div class="lac-panel__body">
                            <p class="lac-panel__label">Available quotas for {{ $selectedYear }}</p>

                            <div class="lac-policy-list">
                                @forelse($globalPolicies as $policy)
                                    <label class="lac-policy-item" for="leavePolicy{{ $policy->leave_type_id }}">
                                        <input type="checkbox" name="leave_type_ids[]"
                                            value="{{ $policy->leave_type_id }}" class="lac-checkbox leave-checkbox"
                                            id="leavePolicy{{ $policy->leave_type_id }}">
                                        <span class="lac-policy-item__name">{{ $policy->leaveType->name }}</span>
                                        <span class="lac-policy-item__days">{{ $policy->days }}d</span>
                                    </label>
                                @empty
                                    <div class="lac-policy-empty">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No policies configured for {{ $selectedYear }}.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="lac-panel__footer">
                            <button type="submit" class="lac-submit-btn">
                                <i class="fas fa-check-double"></i>
                                Apply to Selected Employees
                            </button>
                            <p class="lac-panel__hint">Only checked employees will be updated.</p>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectAllEmployees = document.getElementById("selectAllEmployees");
            const employeeCheckboxes = document.querySelectorAll(".employee-checkbox");
            const selectAllLeaves = document.getElementById("selectAllLeaves");
            const leaveCheckboxes = document.querySelectorAll(".leave-checkbox");

            // ── Sort assigned rows to top ──────────────────────────────────────
            const tbody = document.querySelector("#employeeDataTable tbody");
            const allRows = Array.from(tbody.querySelectorAll(".data-row"));
            const assignedRows = allRows.filter(r => r.classList.contains("lac-table__row--assigned"));
            const unassignedRows = allRows.filter(r => !r.classList.contains("lac-table__row--assigned"));
            [...assignedRows, ...unassignedRows].forEach(r => tbody.appendChild(r));

            // ── Select-all employees (visible rows only) ───────────────────────
            selectAllEmployees.addEventListener("change", function() {
                const activeVisibleRows = document.querySelectorAll(
                    ".data-row:not([style*='display: none'])");
                activeVisibleRows.forEach(row => {
                    const cb = row.querySelector(".employee-checkbox");
                    if (cb) cb.checked = selectAllEmployees.checked;
                });
            });

            // ── Select-all leave types ─────────────────────────────────────────
            let leavesToggleState = false;
            selectAllLeaves.addEventListener("click", function() {
                leavesToggleState = !leavesToggleState;
                leaveCheckboxes.forEach(cb => {
                    cb.checked = leavesToggleState;
                });
                selectAllLeaves.innerText = leavesToggleState ? "Deselect all" : "Select all";
            });

            // ── Client-side pagination + search ───────────────────────────────
            const searchInput = document.getElementById("datatableSearch");
            const rows = Array.from(document.querySelectorAll(".data-row"));
            const summaryText = document.getElementById("paginationSummary");
            const paginationWrapper = document.getElementById("paginationControlWrapper");

            const rowsPerPage = 10;
            let currentPage = 1;
            let filteredRows = [...rows];

            function renderPagination() {
                const totalRows = filteredRows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;
                if (currentPage > totalPages) currentPage = totalPages;

                const startIdx = (currentPage - 1) * rowsPerPage;
                const endIdx = startIdx + rowsPerPage;

                rows.forEach(row => row.style.display = "none");
                filteredRows.slice(startIdx, endIdx).forEach(row => {
                    row.style.display = "";
                });

                const displayStart = totalRows === 0 ? 0 : startIdx + 1;
                const displayEnd = endIdx > totalRows ? totalRows : endIdx;
                summaryText.innerText = `Showing ${displayStart}–${displayEnd} of ${totalRows} employees`;

                paginationWrapper.innerHTML = "";

                const prevLi = document.createElement("li");
                prevLi.className = `lac-page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML =
                    `<button type="button" class="lac-page-btn"><i class="fas fa-chevron-left"></i></button>`;
                if (currentPage > 1) prevLi.querySelector("button").addEventListener("click", () => {
                    currentPage--;
                    renderPagination();
                });
                paginationWrapper.appendChild(prevLi);

                for (let p = 1; p <= totalPages; p++) {
                    if (totalPages > 5 && Math.abs(p - currentPage) > 1 && p !== 1 && p !== totalPages) {
                        if (p === 2 && currentPage > 3) {
                            const dots = document.createElement("li");
                            dots.className = "lac-page-item disabled";
                            dots.innerHTML = `<span class="lac-page-dots">…</span>`;
                            paginationWrapper.appendChild(dots);
                            p = currentPage - 2;
                        } else if (p === currentPage + 2 && currentPage < totalPages - 2) {
                            const dots = document.createElement("li");
                            dots.className = "lac-page-item disabled";
                            dots.innerHTML = `<span class="lac-page-dots">…</span>`;
                            paginationWrapper.appendChild(dots);
                            p = totalPages - 1;
                        }
                        continue;
                    }
                    const pageLi = document.createElement("li");
                    pageLi.className = `lac-page-item ${p === currentPage ? 'active' : ''}`;
                    pageLi.innerHTML =
                        `<button type="button" class="lac-page-btn ${p === currentPage ? 'active' : ''}">${p}</button>`;
                    pageLi.querySelector("button").addEventListener("click", () => {
                        currentPage = p;
                        renderPagination();
                    });
                    paginationWrapper.appendChild(pageLi);
                }

                const nextLi = document.createElement("li");
                nextLi.className = `lac-page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML =
                    `<button type="button" class="lac-page-btn"><i class="fas fa-chevron-right"></i></button>`;
                if (currentPage < totalPages) nextLi.querySelector("button").addEventListener("click", () => {
                    currentPage++;
                    renderPagination();
                });
                paginationWrapper.appendChild(nextLi);
            }

            searchInput.addEventListener("input", function() {
                const query = searchInput.value.toLowerCase().trim();
                filteredRows = rows.filter(row => {
                    const name = row.querySelector(".data-name").innerText.toLowerCase();
                    const uid = row.querySelector(".data-uid").innerText.toLowerCase();
                    const dept = row.querySelector(".data-dept").innerText.toLowerCase();
                    return name.includes(query) || uid.includes(query) || dept.includes(query);
                });
                currentPage = 1;
                selectAllEmployees.checked = false;
                renderPagination();
            });

            renderPagination();
        });
    </script>

    <style>
        /* ── Root & Layout ─────────────────────────────────────────────────────── */
        .lac-root {
            padding: 28px 32px;
            background: #f0f2f7;
            min-height: 100vh;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .lac-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .lac-layout {
                grid-template-columns: 1fr;
            }

            .lac-root {
                padding: 20px 16px;
            }
        }

        /* ── Header ────────────────────────────────────────────────────────────── */
        .lac-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .lac-header__eyebrow {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #6366f1;
            margin-bottom: 6px;
        }

        .lac-header__title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.6px;
            margin: 0 0 4px;
            line-height: 1.15;
        }

        .lac-header__subtitle {
            font-size: 0.84rem;
            color: #64748b;
            margin: 0;
        }

        /* ── Year Picker ────────────────────────────────────────────────────────── */
        .lac-year-picker {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            cursor: pointer;
            position: relative;
        }

        .lac-year-picker__icon {
            color: #6366f1;
            font-size: 0.9rem;
        }

        .lac-year-picker__label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            display: block;
            margin-bottom: 1px;
        }

        .lac-year-picker__select {
            border: none;
            outline: none;
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            background: transparent;
            cursor: pointer;
            padding: 0;
            appearance: none;
            min-width: 60px;
        }

        .lac-year-picker__caret {
            color: #94a3b8;
            font-size: 0.7rem;
        }

        /* ── Alerts ─────────────────────────────────────────────────────────────── */
        .lac-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 18px;
            border-radius: 8px;
            font-size: 0.855rem;
            font-weight: 500;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .lac-alert--success {
            background: #f0fdf4;
            color: #166534;
            border-color: #22c55e;
        }

        .lac-alert--error {
            background: #fef2f2;
            color: #991b1b;
            border-color: #ef4444;
        }

        .lac-alert__icon {
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* ── Card ───────────────────────────────────────────────────────────────── */
        .lac-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e8edf4;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .lac-card__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        .lac-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid #f1f5f9;
            background: #fafbfc;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* ── Search ─────────────────────────────────────────────────────────────── */
        .lac-search {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 14px;
            min-width: 260px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .lac-search:focus-within {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.10);
        }

        .lac-search__icon {
            color: #94a3b8;
            font-size: 0.82rem;
            flex-shrink: 0;
        }

        .lac-search__input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.84rem;
            color: #0f172a;
            width: 100%;
        }

        .lac-search__input::placeholder {
            color: #b0bec5;
        }

        /* ── Checkbox ───────────────────────────────────────────────────────────── */
        .lac-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .lac-checkbox {
            width: 16px;
            height: 16px;
            accent-color: #6366f1;
            cursor: pointer;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .lac-checkbox-label__text {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
        }

        /* ── Table ──────────────────────────────────────────────────────────────── */
        .lac-table-wrap {
            overflow-x: auto;
        }

        .lac-table {
            width: 100%;
            border-collapse: collapse;
        }

        .lac-table__th {
            padding: 11px 16px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            white-space: nowrap;
            text-align: left;
        }

        .lac-table__th--check {
            width: 52px;
            text-align: center;
        }

        .lac-table__td {
            padding: 13px 16px;
            border-bottom: 1px solid #f8fafc;
            vertical-align: middle;
        }

        .lac-table__td--check {
            text-align: center;
        }

        .lac-table__row {
            transition: background 0.12s;
        }

        .lac-table__row:hover {
            background: #fafbff;
        }

        .lac-table__row:last-child .lac-table__td {
            border-bottom: none;
        }

        /* Assigned rows — pinned to top with a left accent */
        .lac-table__row--assigned {
            background: #fdfcff;
            border-left: 3px solid #6366f1;
        }

        .lac-table__row--assigned:hover {
            background: #f5f4ff;
        }

        /* ── Employee Cell ───────────────────────────────────────────────────────── */
        .lac-employee {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .lac-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            letter-spacing: 0.5px;
        }

        .lac-avatar::after {
            content: attr(data-initials);
        }

        .lac-employee__info {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .lac-employee__name {
            font-size: 0.86rem;
            font-weight: 650;
            color: #0f172a;
        }

        .lac-employee__uid {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Assigned badge on employee name */
        .lac-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 0.67rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .lac-badge--assigned {
            background: #eef2ff;
            color: #6366f1;
            border: 1px solid #c7d2fe;
            margin-left: 6px;
        }

        /* ── Dept ───────────────────────────────────────────────────────────────── */
        .lac-dept {
            font-size: 0.83rem;
            color: #475569;
            font-weight: 500;
        }

        /* ── Allowance Chips ─────────────────────────────────────────────────────── */
        .lac-alloc-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .lac-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 5px;
            padding: 3px 8px;
            font-size: 0.72rem;
        }

        .lac-chip__code {
            font-weight: 700;
            color: #0369a1;
        }

        .lac-chip__days {
            font-weight: 600;
            color: #0284c7;
        }

        .lac-unassigned {
            font-size: 0.75rem;
            color: #cbd5e1;
            font-style: italic;
        }

        /* ── Pagination ─────────────────────────────────────────────────────────── */
        .lac-pagination__summary {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 500;
        }

        .lac-pagination {
            display: flex;
            align-items: center;
            gap: 3px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .lac-page-item {
            list-style: none;
        }

        .lac-page-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #fff;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.13s;
        }

        .lac-page-btn:hover {
            background: #f8fafc;
            border-color: #6366f1;
            color: #6366f1;
        }

        .lac-page-btn.active {
            background: #6366f1;
            border-color: #6366f1;
            color: #fff;
        }

        .lac-page-item.disabled .lac-page-btn {
            opacity: 0.4;
            cursor: default;
            pointer-events: none;
        }

        .lac-page-dots {
            font-size: 0.78rem;
            color: #94a3b8;
            padding: 0 4px;
            display: flex;
            align-items: center;
            height: 30px;
        }

        /* ── Right Panel ─────────────────────────────────────────────────────────── */
        .lac-layout__aside {
            position: sticky;
            top: 20px;
        }

        .lac-panel {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e8edf4;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .lac-panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .lac-panel__title-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lac-panel__icon {
            color: #6366f1;
            font-size: 0.9rem;
        }

        .lac-panel__title {
            font-size: 0.88rem;
            font-weight: 750;
            color: #0f172a;
            margin: 0;
        }

        .lac-link-btn {
            background: none;
            border: none;
            padding: 0;
            font-size: 0.78rem;
            font-weight: 650;
            color: #6366f1;
            cursor: pointer;
            text-decoration: none;
        }

        .lac-link-btn:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        .lac-panel__body {
            padding: 18px 20px;
        }

        .lac-panel__label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            margin: 0 0 12px;
        }

        /* ── Policy List ─────────────────────────────────────────────────────────── */
        .lac-policy-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-height: 260px;
            overflow-y: auto;
            margin-bottom: 4px;
        }

        .lac-policy-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.12s;
            border: 1px solid transparent;
        }

        .lac-policy-item:hover {
            background: #f5f4ff;
            border-color: #e0e7ff;
        }

        .lac-policy-item__name {
            flex: 1;
            font-size: 0.84rem;
            font-weight: 500;
            color: #1e293b;
        }

        .lac-policy-item__days {
            font-size: 0.75rem;
            font-weight: 700;
            color: #16a34a;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 5px;
            padding: 2px 8px;
            white-space: nowrap;
        }

        .lac-policy-empty {
            text-align: center;
            padding: 20px;
            font-size: 0.82rem;
            color: #ef4444;
            font-weight: 600;
        }

        .lac-policy-empty i {
            margin-right: 6px;
        }

        /* ── Panel Footer ───────────────────────────────────────────────────────── */
        .lac-panel__footer {
            padding: 16px 20px;
            border-top: 1px solid #f1f5f9;
            background: #fafbfc;
        }

        .lac-submit-btn {
            width: 100%;
            height: 46px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.30);
            transition: opacity 0.15s, box-shadow 0.15s, transform 0.12s;
            letter-spacing: 0.1px;
        }

        .lac-submit-btn:hover {
            opacity: 0.93;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.38);
            transform: translateY(-1px);
        }

        .lac-submit-btn:active {
            transform: translateY(0);
        }

        .lac-panel__hint {
            margin: 10px 0 0;
            font-size: 0.72rem;
            color: #94a3b8;
            text-align: center;
        }

        .lac-select-all-wrap {
            white-space: nowrap;
        }
    </style>
@endsection
