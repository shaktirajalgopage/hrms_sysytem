<aside id="sidebar" class="sidebar js-sidebar" aria-label="Main navigation">
    <div class="sidebar-content js-simplebar">

        <div class="sidebar-brand-row">
            <a class="sidebar-brand">
                <span class="sidebar-brand-mark"><img src="{{ asset('img/photos/fab_icon.png') }}" alt=""
                        class="sidebar-logo">

                </span>
                <span class="sidebar-brand-text">ALGOPAGE.</span>
            </a>

            <button class="sidebar-close-btn d-lg-none" id="sidebarMobileClose" type="button" aria-label="Close menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <ul class="sidebar-nav" id="sidebarAccordion">

            {{-- ============================================================ --}}
            {{-- DASHBOARD --}}
            {{-- ============================================================ --}}
            <li class="nav-item">
                <a class="nav-link nav-link--flat" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard"
                    href="{{ Auth::user()->role->slug === 'super-admin'
                        ? route('super.dashboard')
                        : (Auth::user()->role->slug === 'administrator'
                            ? route('admin.dashboard')
                            : (Auth::user()->role->slug === 'moderator'
                                ? route('moderator.dashboard')
                                : (Auth::user()->role->slug === 'employee'
                                    ? route('employee.dashboard')
                                    : (Auth::user()->role->slug === 'hr-manager'
                                        ? route('hr.dashboard')
                                        : route('payroll.dashboard'))))) }}">
                    <span class="nav-link-icon"><i data-feather="sliders"></i></span>
                    <span class="nav-link-text">{{ __('Dashboard') }}</span>
                </a>
            </li>

            {{-- ============================================================ --}}
            {{-- USERS MANAGEMENT --}}
            {{-- ============================================================ --}}
            @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator'))
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navUsers" aria-expanded="false" aria-controls="navUsers">
                        <span class="nav-link-icon"><i class="fas fa-user"></i></span>
                        <span class="nav-link-text">{{ __('Users Management') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>
                    <div class="collapse nav-submenu" id="navUsers" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin' ? route('user.index') : route('admin.users.index') }}">
                                    <span class="nav-link-icon"><i class="fas fa-user"></i></span>
                                    <span class="nav-link-text">{{ __('Manage Users') }}</span>
                                </a>
                            </li>
                            @if (Auth::check() && Auth::user()->role->slug === 'super-admin')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('roles.index') }}">
                                        <span class="nav-link-icon"><i class="fas fa-user-shield"></i></span>
                                        <span class="nav-link-text">{{ __('User Settings') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            @if (Auth::check() &&
                    (Auth::user()->role->slug === 'super-admin' ||
                        Auth::user()->role->slug === 'administrator' ||
                        Auth::user()->role->slug === 'hr-manager'))
                {{-- ============================================================ --}}
                {{-- EMPLOYEE MANAGEMENT --}}
                {{-- ============================================================ --}}
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navEmployee" aria-expanded="false" aria-controls="navEmployee">
                        <span class="nav-link-icon"><i class="fa-solid fa-users-viewfinder"></i></span>
                        <span class="nav-link-text">{{ __('Employee Management') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>
                    <div class="collapse nav-submenu" id="navEmployee" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('employee.index')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.employee.index')
                                            : route('hr.employee.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-users-viewfinder"></i></span>
                                    <span class="nav-link-text">{{ __('Manage Employees') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('employee-assets.index')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.employee-assets.index')
                                            : route('employee-assets.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-laptop"></i></span>
                                    <span class="nav-link-text">{{ __('Asset Management') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('user-notifications.index')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.user-notifications.index')
                                            : route('hr.user-notifications.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-bell"></i></span>
                                    <span class="nav-link-text">{{ __('User Notification Management') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{{ Auth::user()->role->slug === 'super-admin' ? route('positions.index'): route('hr.positions.index') }}}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-people-arrows"></i></span>
                                    <span class="nav-link-text">{{ __('Interview Management') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{{ Auth::user()->role->slug === 'super-admin' ? route('applications.index'): route('hr.applications.index') }}}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-file-lines"></i></span>
                                    <span class="nav-link-text">{{ __('Application Management') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- ============================================================ --}}
                {{-- INVENTORY MANAGEMENT --}}
                {{-- ============================================================ --}}
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navInventory" aria-expanded="false" aria-controls="navInventory">
                        <span class="nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                        <span class="nav-link-text">{{ __('Inventory Management') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>
                    <div class="collapse nav-submenu" id="navInventory" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inventory.index') }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                                    <span class="nav-link-text">Inventory</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- ============================================================ --}}
                {{-- SETTINGS --}}
                {{-- ============================================================ --}}
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navSettings" aria-expanded="false" aria-controls="navSettings">
                        <span class="nav-link-icon"><i class="fa-solid fa-gear"></i></span>
                        <span class="nav-link-text">{{ __('Settings') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>

                    <div class="collapse nav-submenu" id="navSettings" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">

                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin' ? route('holiday.index') : route('hr.holiday.index') }}">
                                    <span class="nav-link-icon">
                                        <i class="fa-solid fa-umbrella-beach"></i>
                                    </span>
                                    <span class="nav-link-text">Holiday</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin' ? route('salary-slip.index') : route('hr.salary-slip.index') }}">
                                    <span class="nav-link-icon">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </span>
                                    <span class="nav-link-text">Salary-slip</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('leave-allocation.index')
                                        : route('hr.leave-allocation.index') }}">
                                    <span class="nav-link-icon">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </span>
                                    <span class="nav-link-text">Leave Allocation</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('employee-leave.assignments.index')
                                        : route('hr.employee-leave.assignments.index') }}">
                                    <span class="nav-link-icon">
                                        <i class="fa-solid fa-calendar-plus"></i>
                                    </span>
                                    <span class="nav-link-text">Assign Leave Allocation</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                {{-- ============================================================ --}}
                {{-- ORGANIZATION MANAGEMENT --}}
                {{-- ============================================================ --}}
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navOrganization" aria-expanded="false" aria-controls="navOrganization">
                        <span class="nav-link-icon"><i class="fa-solid fa-building"></i></span>
                        <span class="nav-link-text">{{ __('Organization Management') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>
                    <div class="collapse nav-submenu" id="navOrganization" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('department.index')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.department.index')
                                            : route('hr.department.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-building"></i></span>
                                    <span class="nav-link-text">{{ __('Manage Departments') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('designation.index')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.designation.index')
                                            : route('hr.designation.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-user-tie"></i></span>
                                    <span class="nav-link-text">{{ __('Manage Designations') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif


{{-- ============================================================ --}}
{{-- TASK MANAGEMENT --}}
{{-- ============================================================ --}}
@if (Auth::check())
    <li class="nav-item nav-group">
        <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
            data-bs-target="#navTaskManagement" aria-expanded="false" aria-controls="navTaskManagement">
            <span class="nav-link-icon"><i class="fa-solid fa-diagram-project"></i></span>
            <span class="nav-link-text">{{ __('Task Management') }}</span>
            <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
        </a>
        <div class="collapse nav-submenu" id="navTaskManagement" data-bs-parent="#sidebarAccordion">
            <ul class="nav-sublist">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}"
                        href="{{ route('projects.index') }}">
                        <span class="nav-link-icon"><i class="fa-solid fa-diagram-project"></i></span>
                        <span class="nav-link-text">{{ __('Projects') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}"
                        href="{{ route('tickets.index') }}">
                        <span class="nav-link-icon"><i class="fa-solid fa-ticket"></i></span>
                        <span class="nav-link-text">{{ __('Tickets') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}"
                        href="{{ route('reports.index') }}">
                        <span class="nav-link-icon"><i class="fa-solid fa-chart-line"></i></span>
                        <span class="nav-link-text">{{ __('Reports') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif

            {{-- ============================================================ --}}
            {{-- ATTENDANCE MANAGEMENT --}}
            {{-- ============================================================ --}}
            @if (Auth::check() &&
                    (Auth::user()->role->slug === 'super-admin' ||
                        Auth::user()->role->slug === 'hr-manager' ||
                        Auth::user()->role->slug === 'administrator' ||
                        Auth::user()->role->slug === 'moderator'))
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navAttendance" aria-expanded="false" aria-controls="navAttendance">
                        <span class="nav-link-icon"><i class="fa-solid fa-clock"></i></span>
                        <span class="nav-link-text">{{ __('Attendance Management') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>
                    <div class="collapse nav-submenu" id="navAttendance" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin' ? route('schedule.index') : (Auth::user()->role->slug === 'administrator' ? route('admin.schedule.index') : route('moderator.schedule.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-clock"></i></span>
                                    <span class="nav-link-text">{{ __('Schedule') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('attendance-list.index')
                                        : (Auth::user()->role->slug === 'hr-manager'
                                            ? route('hr.attendance-list.index')
                                            : route('moderator.attendance-list.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-list-check"></i></span>
                                    <span class="nav-link-text">{{ __('Check Daily Attendance') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('notifications.index')
                                        : (Auth::user()->role->slug === 'hr-manager'
                                            ? route('hr.notifications.index')
                                            : route('moderator.notifications.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-bell"></i></span>
                                    <span class="nav-link-text">{{ __('Notification Settings') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('attendance.index')
                                        : (Auth::user()->role->slug === 'hr-manager'
                                            ? route('hr.attendance.index')
                                            : route('moderator.attendance.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                    <span class="nav-link-text">{{ __('Daily Attendance') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin' ? route('sheet.report') : (Auth::user()->role->slug === 'administrator' ? route('admin.sheet.report') : route('moderator.sheet.report')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-book"></i></span>
                                    <span class="nav-link-text">{{ __('Sheet Report') }}</span>
                                </a>
                            </li>

                            {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('late.time') }}">
              <span class="nav-link-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
              <span class="nav-link-text">{{ __('Late Time') }}</span>
            </a>
            </li> --}}

                            {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('over.time') }}">
              <span class="nav-link-icon"><i class="fa-solid fa-stopwatch"></i></span>
              <span class="nav-link-text">{{ __('Over Time') }}</span>
            </a>
            </li> --}}
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ============================================================ --}}
            {{-- LEAVE MANAGEMENT (+ personal employee items) --}}
            {{-- ============================================================ --}}
            @if (Auth::check() &&
                    (Auth::user()->role->slug === 'super-admin' ||
                        Auth::user()->role->slug === 'administrator' ||
                        Auth::user()->role->slug === 'hr-manager' ||
                        Auth::user()->role->slug === 'employee'))
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navLeave" aria-expanded="false" aria-controls="navLeave">
                        <span class="nav-link-icon"><i class="fa-solid fa-person-walking-arrow-right"></i></span>
                        <span class="nav-link-text">{{ __('Leave Management') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>
                    <div class="collapse nav-submenu" id="navLeave" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('leaves.index')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.leaves.index')
                                            : (Auth::user()->role->slug === 'employee'
                                                ? route('employee.leaves.index')
                                                : route('hr.leaves.index'))) }}">
                                    <span class="nav-link-icon"><i
                                            class="fa-solid fa-person-walking-arrow-right"></i></span>
                                    <span class="nav-link-text">{{ __('Manage Leaves') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('leaves.create')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.leaves.create')
                                            : (Auth::user()->role->slug === 'employee'
                                                ? route('employee.leaves.create')
                                                : route('hr.leaves.create'))) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-file-pen"></i></span>
                                    <span class="nav-link-text">{{ __('Apply Leave') }}</span>
                                </a>
                            </li>

                            @if (Auth::check() && Auth::user()->role->slug === 'employee')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('employee.assets') }}">
                                        <span class="nav-link-icon"><i class="fa-solid fa-laptop"></i></span>
                                        <span class="nav-link-text">My Assets</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('employee.holidays') }}">
                                        <span class="nav-link-icon"><i class="fa-solid fa-calendar-day"></i></span>
                                        <span class="nav-link-text">My Holidays</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('salary-slip.my-slips') }}">
                                        <span class="nav-link-icon"><i
                                                class="fa-solid fa-file-invoice-dollar"></i></span>
                                        <span class="nav-link-text">My Payslips</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ============================================================ --}}
            {{-- PAYROLL SYSTEM --}}
            {{-- ============================================================ --}}
            @if (Auth::check() &&
                    (Auth::user()->role->slug === 'super-admin' ||
                        Auth::user()->role->slug === 'administrator' ||
                        Auth::user()->role->slug === 'payroll-manager'))
                <li class="nav-item nav-group">
                    <a class="nav-link nav-link--toggle" href="#" role="button" data-bs-toggle="collapse"
                        data-bs-target="#navPayroll" aria-expanded="false" aria-controls="navPayroll">
                        <span class="nav-link-icon"><i class="fa-solid fa-file"></i></span>
                        <span class="nav-link-text">{{ __('Payroll System') }}</span>
                        <span class="nav-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                    </a>
                    <div class="collapse nav-submenu" id="navPayroll" data-bs-parent="#sidebarAccordion">
                        <ul class="nav-sublist">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ Auth::user()->role->slug === 'super-admin' ? route('payroll.index') : (Auth::user()->role->slug === 'administrator' ? route('admin.payroll.index') : route('manager.payroll.index')) }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-file"></i></span>
                                    <span class="nav-link-text">{{ __('Manage Payroll') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('payroll.create') }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-file-export"></i></span>
                                    <span class="nav-link-text">{{ __('Generate Payroll') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('payroll.report') }}">
                                    <span class="nav-link-icon"><i class="fa-solid fa-file-export"></i></span>
                                    <span class="nav-link-text">{{ __('Payroll Sheet') }}</span>
                                </a>
                            </li>

                            {{-- <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)">
              <span class="nav-link-icon"><i class="fa-solid fa-wallet"></i></span>
              <span class="nav-link-text">{{ __('Gross Salary') }}</span>
            </a>
            </li> --}}

                            {{-- <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)">
              <span class="nav-link-icon"><i class="fa-solid fa-clipboard"></i></span>
              <span class="nav-link-text">{{ __('Deductions') }}</span>
            </a>
            </li> --}}
                        </ul>
                    </div>
                </li>
            @endif

        </ul>
    </div>
</aside>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<style>
    :root {
        --sb-primary: #2563EB;
        --sb-primary-rgb: 37, 99, 235;
        --sb-bg: #0F172A;
        --sb-bg-secondary: #1E293B;
        --sb-hover: rgba(37, 99, 235, 0.12);
        --sb-active: #2563EB;
        --sb-text-primary: #FFFFFF;
        --sb-text-secondary: #94A3B8;
        --sb-border: rgba(255, 255, 255, 0.08);
        --sb-width: 280px;
        --sb-width-collapsed: 80px;
        --sb-radius: 10px;
        --sb-ease: cubic-bezier(.4, 0, .2, 1);
        --sb-speed: 250ms;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .sidebar.js-sidebar {
        width: var(--sb-width);
        min-height: 100vh;
        background: linear-gradient(180deg, var(--sb-bg) 0%, var(--sb-bg-secondary) 100%);
        border-right: 1px solid var(--sb-border);
        display: flex;
        flex-direction: column;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1030;
        transition: width var(--sb-speed) var(--sb-ease), transform var(--sb-speed) var(--sb-ease);
        font-family: "Inter", "Segoe UI", Roboto, system-ui, sans-serif;
    }

    .sidebar.js-sidebar.sidebar-collapsed {
        width: var(--sb-width-collapsed);
    }

    .sidebar-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        color: white;
        bgcolor: transparent;
    }

    .sidebar-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 0 14px 24px;
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, .18) transparent;
    }

    .sidebar-content::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .16);
        border-radius: 10px;
    }

    .sidebar-content::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, .28);
    }

    /* Brand row */
    .sidebar-brand-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 20px 6px 18px;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        flex: 1;
        min-width: 0;
        color: var(--sb-text-primary);
    }

    .sidebar-brand-mark {
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 16px;
        color: #fff;
        background: #ffffff;
    }

    .sidebar-brand-text {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: .2px;
        color: var(--sb-text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: opacity var(--sb-speed) var(--sb-ease);
    }

    .sidebar-collapsed .sidebar-brand-text {
        opacity: 0;
        width: 0;
    }

    .sidebar-pin-btn,
    .sidebar-close-btn {
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border: 1px solid var(--sb-border);
        background: rgba(255, 255, 255, .03);
        color: var(--sb-text-secondary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: background var(--sb-speed) var(--sb-ease), color var(--sb-speed) var(--sb-ease), transform var(--sb-speed) var(--sb-ease);
    }

    .sidebar-pin-btn:hover,
    .sidebar-close-btn:hover {
        background: var(--sb-primary);
        color: #fff;
        border-color: var(--sb-primary);
    }

    .sidebar-pin-btn:focus-visible,
    .sidebar-close-btn:focus-visible,
    .nav-link:focus-visible {
        outline: 2px solid #fff;
        outline-offset: 2px;
    }

    .sidebar-collapsed .sidebar-pin-btn i {
        transform: rotate(180deg);
    }

    .sidebar-pin-btn i {
        transition: transform var(--sb-speed) var(--sb-ease);
    }

    /* Nav */
    .sidebar-nav {
        list-style: none;
        margin: 6px 0 0;
        padding: 0;
        flex: 1;
    }

    .nav-item {
        margin-bottom: 2px;
    }

    .nav-group {
        margin-top: 6px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        border-radius: var(--sb-radius);
        color: var(--sb-text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        line-height: 1.2;
        white-space: nowrap;
        position: relative;
        transition: background var(--sb-speed) var(--sb-ease), color var(--sb-speed) var(--sb-ease);
    }

    .nav-link:hover {
        background: var(--sb-hover);
        color: var(--sb-text-primary);
    }

    .nav-link:focus {
        color: var(--sb-text-primary);
    }

    .nav-link-icon {
        flex: 0 0 20px;
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        line-height: 1;
        color: var(--sb-text-secondary);
        transition: color var(--sb-speed) var(--sb-ease);
    }

    .nav-link-icon svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
    }

    .nav-link:hover .nav-link-icon,
    .nav-link.active .nav-link-icon {
        color: #fff;
    }

    .nav-link-text {
        overflow: hidden;
        text-overflow: ellipsis;
        transition: opacity var(--sb-speed) var(--sb-ease);
    }

    /* Active state */
    .nav-link.active {
        background: var(--sb-active);
        color: #fff;
        font-weight: 600;
        box-shadow: 0 4px 14px rgba(var(--sb-primary-rgb), .35);
    }

    .nav-link--toggle.active-parent {
        color: var(--sb-text-primary);
        background: rgba(255, 255, 255, .04);
    }

    .nav-link--toggle.active-parent .nav-link-icon {
        color: var(--sb-primary);
    }

    /* Toggle + chevron */
    .nav-link--toggle {
        cursor: pointer;
    }

    .nav-link--toggle .nav-link-text {
        flex: 1;
    }

    .nav-chevron {
        flex: 0 0 auto;
        font-size: 10px;
        color: var(--sb-text-secondary);
        transition: transform var(--sb-speed) var(--sb-ease);
    }

    .nav-link--toggle[aria-expanded="true"] .nav-chevron {
        transform: rotate(180deg);
    }

    .nav-link--toggle[aria-expanded="true"] {
        color: var(--sb-text-primary);
    }

    /* Submenu (Bootstrap .collapse handles height animation) */
    .nav-submenu {
        margin-left: 10px;
    }

    .nav-sublist {
        list-style: none;
        margin: 4px 0 6px;
        padding: 4px 0 4px 16px;
        border-left: 1px solid var(--sb-border);
    }

    .nav-sublist .nav-link {
        padding: 9px 10px;
        font-size: 13px;
        font-weight: 450;
    }

    .nav-sublist .nav-link-icon {
        font-size: 12px;
    }

    /* Icon-only (collapsed) mode: flyout glassmorphism submenus */
    .sidebar-collapsed .nav-link-text,
    .sidebar-collapsed .nav-chevron {
        display: none;
    }

    .sidebar-collapsed .sidebar-brand-mark {
        margin: 0 auto;
    }

    .sidebar-collapsed .nav-link {
        justify-content: center;
    }

    .sidebar-collapsed .nav-link-icon {
        margin: 0;
    }

    .sidebar-collapsed .nav-group {
        position: relative;
    }

    .sidebar-collapsed .nav-submenu.collapse {
        display: block !important;
        position: absolute;
        left: calc(100% + 10px);
        top: 0;
        min-width: 240px;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        background: rgba(30, 41, 59, .85);
        backdrop-filter: blur(14px) saturate(140%);
        -webkit-backdrop-filter: blur(14px) saturate(140%);
        border: 1px solid var(--sb-border);
        border-radius: var(--sb-radius);
        box-shadow: 0 20px 45px rgba(0, 0, 0, .5);
        transform: translateY(6px);
        transition: opacity var(--sb-speed) var(--sb-ease), transform var(--sb-speed) var(--sb-ease), visibility var(--sb-speed) var(--sb-ease);
        z-index: 1040;
    }

    .sidebar-collapsed .nav-group:hover .nav-submenu.collapse,
    .sidebar-collapsed .nav-group:focus-within .nav-submenu.collapse {
        max-height: 600px;
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .sidebar-collapsed .nav-sublist {
        border-left: 0;
        padding: 6px;
        margin: 0;
    }

    .sidebar-collapsed .nav-sublist .nav-link {
        justify-content: flex-start;
    }

    .sidebar-collapsed .nav-sublist .nav-link-text {
        display: inline;
        opacity: 1;
    }

    /* Bootstrap tooltip contrast tweak for dark sidebar */
    .tooltip .tooltip-inner {
        background: var(--sb-bg-secondary);
        color: var(--sb-text-primary);
        border: 1px solid var(--sb-border);
        font-size: 12.5px;
    }

    .tooltip.bs-tooltip-end .tooltip-arrow::before,
    .tooltip.bs-tooltip-auto[data-popper-placement^="right"] .tooltip-arrow::before {
        border-right-color: var(--sb-bg-secondary);
    }

    /* Mobile drawer */
    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(2, 6, 23, .55);
        backdrop-filter: blur(2px);
        z-index: 1025;
        opacity: 0;
        transition: opacity var(--sb-speed) var(--sb-ease);
    }

    .sidebar-backdrop.show {
        display: block;
        opacity: 1;
    }

    @media (max-width:991.98px) {
        .sidebar.js-sidebar {
            transform: translateX(-100%);
            width: var(--sb-width);
            box-shadow: 0 0 60px rgba(0, 0, 0, .55);
        }

        .sidebar.js-sidebar.sidebar-mobile-open {
            transform: translateX(0);
        }

        .sidebar-pin-btn {
            display: none !important;
        }

        .sidebar-collapsed .nav-link-text,
        .sidebar-collapsed .nav-chevron {
            display: inline-flex;
        }
    }

    @media (min-width:992px) {
        .sidebar-close-btn {
            display: none !important;
        }
    }

    @media (prefers-reduced-motion:reduce) {

        .sidebar.js-sidebar,
        .nav-link,
        .nav-link-text,
        .nav-chevron,
        .nav-submenu.collapse,
        .sidebar-pin-btn i {
            transition: none !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        var sidebar = document.getElementById('sidebar');
        var pinBtn = document.getElementById('sidebarCollapseToggle');
        var closeBtn = document.getElementById('sidebarMobileClose');
        var backdrop = document.getElementById('sidebarBackdrop');
        var navLinks = Array.prototype.slice.call(sidebar.querySelectorAll('.nav-link:not(.nav-link--toggle)'));
        var subLinks = Array.prototype.slice.call(sidebar.querySelectorAll('.nav-submenu .nav-link'));
        var toggles = Array.prototype.slice.call(sidebar.querySelectorAll('.nav-link--toggle'));
        var collapses = Array.prototype.slice.call(sidebar.querySelectorAll('.nav-submenu.collapse'));

        var LS_COLLAPSED = 'algopage_sb_collapsed';
        var LS_OPEN_ID = 'algopage_sb_open_group';

        function whenBootstrapReady(cb) {
            if (window.bootstrap && window.bootstrap.Collapse) {
                cb();
                return;
            }
            var iv = setInterval(function() {
                if (window.bootstrap && window.bootstrap.Collapse) {
                    clearInterval(iv);
                    cb();
                }
            }, 30);
        }

        /* ---------- 1. Active route detection + auto-expand parent ---------- */
        var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
        var activeParentId = null;

        navLinks.concat(subLinks).forEach(function(link) {
            try {
                var linkPath = new URL(link.getAttribute('href'), window.location.origin).pathname
                    .replace(/\/+$/, '') || '/';
                if (linkPath === currentPath) {
                    link.classList.add('active');
                    link.setAttribute('aria-current', 'page');
                    var submenu = link.closest('.nav-submenu.collapse');
                    if (submenu) {
                        activeParentId = submenu.id;
                        var parentToggle = sidebar.querySelector('[data-bs-target="#' + submenu.id +
                            '"]');
                        if (parentToggle) parentToggle.classList.add('active-parent');
                    }
                }
            } catch (e) {
                /* skip malformed href */ }
        });

        /* ---------- 2. Accordion open state (Bootstrap Collapse + localStorage) ---------- */
        whenBootstrapReady(function() {
            var instances = {};
            collapses.forEach(function(el) {
                instances[el.id] = new bootstrap.Collapse(el, {
                    toggle: false
                });
            });

            var storedId = localStorage.getItem(LS_OPEN_ID);
            var openId = activeParentId || storedId;
            if (openId && instances[openId]) {
                instances[openId].show();
                var t = sidebar.querySelector('[data-bs-target="#' + openId + '"]');
                if (t) t.setAttribute('aria-expanded', 'true');
            }

            collapses.forEach(function(el) {
                el.addEventListener('shown.bs.collapse', function() {
                    localStorage.setItem(LS_OPEN_ID, el.id);
                });
                el.addEventListener('hidden.bs.collapse', function() {
                    if (localStorage.getItem(LS_OPEN_ID) === el.id) {
                        localStorage.removeItem(LS_OPEN_ID);
                    }
                });
            });
        });

        /* ---------- 3. Sidebar icon-only collapse (desktop) ---------- */
        function applyCollapsed(state) {
            sidebar.classList.toggle('sidebar-collapsed', state);
            pinBtn.setAttribute('aria-pressed', state ? 'true' : 'false');
            pinBtn.setAttribute('title', state ? 'Expand sidebar' : 'Collapse sidebar');
            refreshTooltips(state);
        }
        applyCollapsed(localStorage.getItem(LS_COLLAPSED) === '1');

        pinBtn.addEventListener('click', function() {
            var next = !sidebar.classList.contains('sidebar-collapsed');
            applyCollapsed(next);
            localStorage.setItem(LS_COLLAPSED, next ? '1' : '0');
        });

        /* ---------- 4. Tooltips (only meaningful in icon-only mode) ---------- */
        var tooltipInstances = [];

        function refreshTooltips(collapsedState) {
            whenBootstrapReady(function() {
                tooltipInstances.forEach(function(t) {
                    t.dispose();
                });
                tooltipInstances = [];
                if (!collapsedState) return;
                var targets = sidebar.querySelectorAll('[data-bs-toggle="tooltip"]');
                targets.forEach(function(el) {
                    tooltipInstances.push(new bootstrap.Tooltip(el));
                });
            });
        }

        /* ---------- 5. Mobile drawer ---------- */
        function openMobile() {
            sidebar.classList.add('sidebar-mobile-open');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobile() {
            sidebar.classList.remove('sidebar-mobile-open');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
        document.querySelectorAll('.js-sidebar-toggle, #sidebarToggle').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.contains('sidebar-mobile-open') ? closeMobile() :
            openMobile();
            });
        });
        closeBtn.addEventListener('click', closeMobile);
        backdrop.addEventListener('click', closeMobile);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('sidebar-mobile-open')) closeMobile();
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) closeMobile();
        });

        /* ---------- 6. Feather icon render (Dashboard uses data-feather) ---------- */
        if (window.feather) {
            window.feather.replace();
        }
    });
</script>
