<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <ul class="navbar-nav navbar-align">
        <li class="nav-item dropdown">
            <a class="nav-icon nav-link dropdown-toggle" href="javascript:void(0)" id="itemsDropdown"
                data-bs-toggle="dropdown">
                <i class="align-middle" data-feather="plus"></i>
                <span class="align-middle" style="font-size: 0.85rem;">New Items</span>
            </a>
            <div class="dropdown-menu py-0" aria-labelledby="itemsDropdown">
                <div class="dropdown-menu-header">{{ __('Add New Option') }}</div>
                <div class="list-group">
                    <a href="{{ route('department.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Department') }}</span>
                    </a>
                    <a href="{{ route('designation.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Designation') }}</span>
                    </a>
                    <a href="{{ route('employee.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Employee') }}</span>
                    </a>
                    <a href="{{ route('attendance.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Attendance') }}</span>
                    </a>
                    <a href="{{ route('leaves.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Leave') }}</span>
                    </a>
                    <a href="{{ route('payroll.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Payroll') }}</span>
                    </a>
                    <a href="{{ route('user.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('User') }}</span>
                    </a>
                </div>
            </div>
        </li>
    </ul>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">

            {{-- ───── Check-In Button ───── --}}
            <li class="nav-item" id="navCheckinItem">
                <a class="nav-link" href="javascript:void(0)" id="navCheckinBtn"
                    onclick="openAttendanceModal('checkin')" title="Check In">
                    <i class="align-middle text-success" data-feather="log-in"></i>
                    <span class="align-middle d-none d-sm-inline ms-1 text-success"
                        style="font-size:0.85rem;font-weight:600;">Check In</span>
                </a>
            </li>

            {{-- ───── Check-Out Button ───── --}}
            <li class="nav-item me-2" id="navCheckoutItem">
                <a class="nav-link disabled opacity-50" href="javascript:void(0)" id="navCheckoutBtn"
                    title="Check Out — check in first" style="pointer-events:none; cursor:not-allowed;">
                    <i class="align-middle text-warning" data-feather="log-out"></i>
                    <span class="align-middle d-none d-sm-inline ms-1 text-warning"
                        style="font-size:0.85rem;font-weight:600;">Check Out</span>
                </a>
            </li>

            {{-- ───── Notifications ───── --}}
            {{-- ───── Notifications ───── --}}
            <li class="nav-item dropdown">


                <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">


                    <div class="position-relative">


                        <i class="align-middle" data-feather="bell"></i>



                        @if ($unreadNotifications > 0)
                            <span class="indicator">

                                {{ $unreadNotifications }}

                            </span>
                        @endif



                    </div>


                </a>





                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">



                    <div class="dropdown-menu-header">


                        @if ($unreadNotifications)
                            {{ $unreadNotifications }} New Notifications
                        @else
                            No New Notifications
                        @endif


                    </div>





                    <div class="list-group">


                        @forelse($notifications as $notification)
                            <a href="{{ route('notification.read', $notification->id) }}" class="list-group-item">


                                <div class="row g-0 align-items-center">


                                    <div class="col-2">


                                        @if (($notification->data['type'] ?? '') == 'danger')
                                            <i class="text-danger" data-feather="alert-circle"></i>
                                        @elseif(($notification->data['type'] ?? '') == 'warning')
                                            <i class="text-warning" data-feather="bell"></i>
                                        @else
                                            <i class="text-success" data-feather="check-circle"></i>
                                        @endif


                                    </div>




                                    <div class="col-10">


                                        <div class="text-dark">


                                            {{ $notification->data['title'] }}


                                            @if (!$notification->read_at)
                                                <span class="badge bg-primary">
                                                    New
                                                </span>
                                            @endif


                                        </div>





                                        <div class="text-muted small mt-1">


                                            {{ $notification->data['message'] }}


                                        </div>




                                        <div class="text-muted small mt-1">


                                            {{ $notification->created_at->diffForHumans() }}


                                        </div>



                                    </div>



                                </div>


                            </a>



                        @empty


                            <div class="text-center p-3 text-muted">


                                No notifications


                            </div>
                        @endforelse



                    </div>




                    <div class="dropdown-menu-footer">


                        <a href="{{ route('notifications.index') }}" class="text-muted">


                            Show all notifications


                        </a>


                    </div>



                </div>


            </li>

            {{-- ───── Messages ───── --}}
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
                    <div class="position-relative">
                        <i class="align-middle" data-feather="message-square"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="messagesDropdown">
                    <div class="dropdown-menu-header">
                        <div class="position-relative">4 New Messages</div>
                    </div>
                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-5.jpg') }}"
                                        class="avatar img-fluid rounded-circle" alt="Vanessa Tucker" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">Vanessa Tucker</div>
                                    <div class="text-muted small mt-1">Nam pretium turpis et arcu. Duis arcu tortor.
                                    </div>
                                    <div class="text-muted small mt-1">15m ago</div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-2.jpg') }}"
                                        class="avatar img-fluid rounded-circle" alt="William Harris" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">William Harris</div>
                                    <div class="text-muted small mt-1">Curabitur ligula sapien euismod vitae.</div>
                                    <div class="text-muted small mt-1">2h ago</div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-4.jpg') }}"
                                        class="avatar img-fluid rounded-circle" alt="Christina Mason" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">Christina Mason</div>
                                    <div class="text-muted small mt-1">Pellentesque auctor neque nec urna.</div>
                                    <div class="text-muted small mt-1">4h ago</div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-3.jpg') }}"
                                        class="avatar img-fluid rounded-circle" alt="Sharon Lessman" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">Sharon Lessman</div>
                                    <div class="text-muted small mt-1">Aenean tellus metus, bibendum sed, posuere ac,
                                        mattis non.</div>
                                    <div class="text-muted small mt-1">5h ago</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="dropdown-menu-footer">
                        <a href="#" class="text-muted">Show all messages</a>
                    </div>
                </div>
            </li>

            {{-- ───── User Dropdown ───── --}}
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#"
                    data-bs-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>
                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#"
                    data-bs-toggle="dropdown">
                    <img src="{{ asset('img/avatars/dummy.png') }}" class="avatar img-fluid rounded me-1"
                        alt="{{ Auth::user()->name }}" />
                    <span class="text-dark">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1"
                            data-feather="user"></i> Profile</a>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1"
                            data-feather="pie-chart"></i> Analytics</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1"
                            data-feather="settings"></i> Settings & Privacy</a>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1"
                            data-feather="help-circle"></i> Help Center</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="align-middle me-1" data-feather="log-out"></i>
                            <span class="me-1">{{ __('Log Out') }}</span>
                        </a>
                    </form>
                </div>
            </li>

        </ul>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════
     ATTENDANCE MODAL (shared for check-in & check-out)
     ═══════════════════════════════════════════════ --}}
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 480px;">
        <div class="modal-content border-0 shadow">

            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span id="modalIconWrap"
                        class="d-inline-flex align-items-center justify-content-center rounded-circle"
                        style="width:36px;height:36px;">
                        <i id="modalIcon" data-feather="log-in" style="width:18px;height:18px;"></i>
                    </span>
                    <h5 class="modal-title mb-0 fw-semibold" id="attendanceModalLabel">Check In</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-3">

                {{-- Checkin time badge (shown during checkout modal) --}}
                <div id="checkinTimeBadge"
     class="alert py-2 px-3 mb-3 align-items-center gap-2 d-none"
                    style="font-size:0.82rem;background:#fff3cd;border:1px solid #ffc107;color:#856404;">
                    <i data-feather="clock" style="width:14px;height:14px;flex-shrink:0;"></i>
                    <span>Checked in at: <strong id="checkinTimeText"></strong></span>
                </div>

                {{-- Map --}}
                <div class="position-relative rounded overflow-hidden border mb-3" style="height:220px;">
                    <div id="attendanceMap" style="width:100%;height:100%;"></div>
                    <div id="mapLoadingOverlay"
                        class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-90">
                        <div class="spinner-border spinner-border-sm text-secondary mb-2" role="status"></div>
                        <small class="text-muted">Fetching your GPS location…</small>
                    </div>
                    <div id="mapPlaceholder"
                        class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center bg-light">
                        <i data-feather="map-pin" class="text-muted mb-1"
                            style="width:32px;height:32px;opacity:0.4;"></i>
                        <small class="text-muted">Map will appear here</small>
                    </div>
                </div>

                {{-- Error alert --}}
                <div id="locationError" class="alert alert-danger d-none py-2 px-3" style="font-size:0.82rem;">
                    <i data-feather="alert-triangle" style="width:14px;height:14px;vertical-align:-2px;"
                        class="me-1"></i>
                    <span id="locationErrorText"></span>
                </div>

                {{-- Location info --}}
                <div id="locationInfo" class="d-none rounded p-2 mb-3" style="font-size:0.82rem;">
                    <div class="fw-semibold mb-1" id="locationInfoTitle">Location detected</div>
                    <div id="locationAddress" class="text-muted"></div>
                    <div id="locationCoords" class="mt-1" style="font-size:0.75rem;opacity:0.7;"></div>
                </div>

            </div>

            <div class="modal-footer border-0 pt-0 gap-2">
                <button type="button" id="refreshLocationBtn"
                    class="btn btn-light btn-sm d-flex align-items-center gap-1" onclick="fetchLocation()">
                    <i data-feather="refresh-cw" style="width:14px;height:14px;"></i> Refresh Location
                </button>
                <button type="button" id="submitAttendanceBtn" class="btn btn-sm d-flex align-items-center gap-1"
                    disabled onclick="submitAttendance()">
                    <i id="submitBtnIcon" data-feather="log-in" style="width:14px;height:14px;"></i>
                    <span id="submitBtnText">Check In Now</span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Toast --}}
{{-- Toast --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div id="attendanceToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
        aria-atomic="true">

        <div class="d-flex">

            <div class="toast-body d-flex align-items-center gap-2" id="toastBody">
            </div>

            <button type="button" class="btn-close me-2 m-auto" id="toastCloseBtn" aria-label="Close">
            </button>

        </div>

    </div>
</div>

<style>
    .swal2-enterprise-popup {
        width: 550px !important;
        max-width: 100% !important;
        border-radius: 18px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        padding: 1.5rem !important;
    }

    .swal2-show-custom {
        animation: swal2-slide-down 0.3s ease-out forwards;
    }

    @keyframes swal2-slide-down {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .swal2-backdrop-blur {
        background: rgba(15, 23, 42, 0.6) !important;
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
    }

    #attendanceMap {
        z-index: 1;
    }

    .modal.fade:not(.show) {
    display: none !important;
}

    

    .transition-all {
        transition: all 0.2s ease-in-out;
    }
</style>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    (function() {
        let map = null;
        let marker = null;
        let currentMode = 'checkin';
        let currentCoords = null;
        let currentAddress = '';
        let submitting = false;
        let activeLogId = null; // ID of the current open check-in session
        let activeCheckinTime = null; // ISO string of when they checked in

        const config = {
            checkin: {
                label: 'Check In',
                featherIcon: 'log-in',
                color: '#198754',
                bgClass: 'bg-success bg-opacity-10',
                textClass: 'text-success',
                btnClass: 'btn-success',
                infoClass: 'bg-success bg-opacity-10',
                infoTitle: 'Location detected',
                route: '{{ route('checkin') }}',
            },
            checkout: {
                label: 'Check Out',
                featherIcon: 'log-out',
                color: '#fd7e14',
                bgClass: 'bg-warning bg-opacity-10',
                textClass: 'text-warning',
                btnClass: 'btn-warning',
                infoClass: 'bg-warning bg-opacity-10',
                infoTitle: 'Location detected',
                route: '{{ route('checkout') }}',
            },
        };

        // ── Button state helper ───────────────────────────────────────────────────
        // isCheckedIn = true  → checkin disabled, checkout enabled
        // isCheckedIn = false → checkin enabled,  checkout disabled
        function setButtonState(isCheckedIn) {
            const checkinBtn = document.getElementById('navCheckinBtn');
            const checkoutBtn = document.getElementById('navCheckoutBtn');

            if (isCheckedIn) {
                // Disable Check In
                checkinBtn.classList.add('disabled', 'opacity-50');
                checkinBtn.style.pointerEvents = 'none';
                checkinBtn.style.cursor = 'not-allowed';
                checkinBtn.title = 'Already checked in — check out first';
                checkinBtn.removeAttribute('onclick');

                // Enable Check Out
                checkoutBtn.classList.remove('disabled', 'opacity-50');
                checkoutBtn.style.pointerEvents = '';
                checkoutBtn.style.cursor = '';
                checkoutBtn.title = 'Check Out';
                checkoutBtn.setAttribute('onclick', "openAttendanceModal('checkout')");
            } else {
                // Enable Check In
                checkinBtn.classList.remove('disabled', 'opacity-50');
                checkinBtn.style.pointerEvents = '';
                checkinBtn.style.cursor = '';
                checkinBtn.title = 'Check In';
                checkinBtn.setAttribute('onclick', "openAttendanceModal('checkin')");

                // Disable Check Out
                checkoutBtn.classList.add('disabled', 'opacity-50');
                checkoutBtn.style.pointerEvents = 'none';
                checkoutBtn.style.cursor = 'not-allowed';
                checkoutBtn.title = 'Check Out — check in first';
                checkoutBtn.removeAttribute('onclick');
            }
        }

        // ── Load status from server on page load ─────────────────────────────────
        async function loadAttendanceStatus() {
            try {
                const res = await fetch('{{ route('status') }}', {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    },
                });
                const data = await res.json();

                if (data.is_checked_in && data.active_log) {
                    activeLogId = data.active_log.id;
                    activeCheckinTime = data.active_log.checkin_at;
                    setButtonState(true);
                } else {
                    activeLogId = null;
                    activeCheckinTime = null;
                    setButtonState(false);
                }
            } catch (e) {
                // silently fail — default state (both visible) is safe
            }
        }

        // ── Open modal ───────────────────────────────────────────────────────────
        window.openAttendanceModal = function(mode) {
            currentMode = mode;
            currentCoords = null;
            currentAddress = '';
            submitting = false;

            const cfg = config[mode];

            // Title & icon
            document.getElementById('attendanceModalLabel').textContent = cfg.label;
            const iconWrap = document.getElementById('modalIconWrap');
            iconWrap.className =
                `d-inline-flex align-items-center justify-content-center rounded-circle ${cfg.bgClass}`;
            iconWrap.style.width = '36px';
            iconWrap.style.height = '36px';
            const modalIcon = document.getElementById('modalIcon');
            modalIcon.setAttribute('data-feather', cfg.featherIcon);
            modalIcon.className = cfg.textClass;

            // Submit button
            const btn = document.getElementById('submitAttendanceBtn');
            btn.className = `btn btn-sm d-flex align-items-center gap-1 ${cfg.btnClass}`;
            btn.disabled = true;
            document.getElementById('submitBtnText').textContent = cfg.label + ' Now';
            document.getElementById('submitBtnIcon').setAttribute('data-feather', cfg.featherIcon);

            // Show checkin time badge when checking out
            const badge = document.getElementById('checkinTimeBadge');
            if (mode === 'checkout' && activeCheckinTime) {
                const dt = new Date(activeCheckinTime);
                document.getElementById('checkinTimeText').textContent = dt.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }) + ', ' + dt.toLocaleDateString();
                badge.classList.remove('d-none');
                badge.classList.add('d-flex');
            } else {
                badge.classList.add('d-none');
                badge.classList.remove('d-flex');
            }

            resetModalUI();
            feather.replace();

            const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
            modal.show();

            fetchLocation();
        };

        // ── Reset UI ─────────────────────────────────────────────────────────────
        function resetModalUI() {
            document.getElementById('locationError').classList.add('d-none');
            document.getElementById('locationInfo').classList.add('d-none');
            document.getElementById('mapPlaceholder').classList.add('d-none');
            document.getElementById('mapLoadingOverlay').classList.remove('d-none');
            document.getElementById('submitAttendanceBtn').disabled = true;
            document.getElementById('refreshLocationBtn').disabled = false;
        }

        // ── Fetch GPS location ────────────────────────────────────────────────────
        window.fetchLocation = function() {
            resetModalUI();

            if (!navigator.geolocation) {
                showLocationError('Geolocation is not supported by your browser.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                onLocationSuccess,
                onLocationError, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        };

        function onLocationSuccess(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            currentCoords = {
                lat,
                lon,
                accuracy
            };

            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=jsonv2`, {
                    headers: {
                        'Accept-Language': 'en',
                        'User-Agent': 'ERP-Attendance/1.0'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    currentAddress = data.display_name || `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                })
                .catch(() => {
                    currentAddress = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                })
                .finally(() => {
                    renderMap(lat, lon);
                    showLocationInfo(lat, lon);
                    document.getElementById('mapLoadingOverlay').classList.add('d-none');
                    document.getElementById('submitAttendanceBtn').disabled = false;
                });
        }

        function onLocationError(error) {
            document.getElementById('mapLoadingOverlay').classList.add('d-none');
            const ph = document.getElementById('mapPlaceholder');
            ph.classList.remove('d-none');
            ph.style.display = 'flex';

            const msgs = {
                [error
                    .PERMISSION_DENIED
                ]: 'GPS access denied. Please enable location permissions in your browser settings and try again.',
                [error
                    .POSITION_UNAVAILABLE
                ]: 'Location unavailable. Please ensure GPS is enabled on your device.',
                [error.TIMEOUT]: 'Location request timed out. Please try again.',
            };
            showLocationError(msgs[error.code] || 'An unknown error occurred while fetching your location.');
            feather.replace();
        }

        function showLocationError(msg) {
            document.getElementById('locationErrorText').textContent = msg;
            document.getElementById('locationError').classList.remove('d-none');
            feather.replace();
        }

        function showLocationInfo(lat, lon) {
            const cfg = config[currentMode];
            const infoEl = document.getElementById('locationInfo');
            infoEl.className = `rounded p-2 mb-3 ${cfg.infoClass}`;
            infoEl.style.fontSize = '0.82rem';

            const titleEl = document.getElementById('locationInfoTitle');
            titleEl.textContent = cfg.infoTitle;
            titleEl.className = `fw-semibold mb-1 ${cfg.textClass}`;

            document.getElementById('locationAddress').textContent = currentAddress || 'Resolving address…';
            document.getElementById('locationCoords').textContent =
                `Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`;
            infoEl.classList.remove('d-none');
        }

        // ── Render Leaflet map ───────────────────────────────────────────────────
        function renderMap(lat, lon) {
            const cfg = config[currentMode];
            const mapEl = document.getElementById('attendanceMap');

            if (map) {
                map.remove();
                map = null;
                marker = null;
            }

            map = L.map(mapEl, {
                zoomControl: true,
                attributionControl: true
            }).setView([lat, lon], 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19
            }).addTo(map);

            const pinIcon = L.divIcon({
                html: `<div style="width:28px;height:28px;background:${cfg.color};border:3px solid #fff;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 28],
                className: ''
            });

            marker = L.marker([lat, lon], {
                icon: pinIcon
            }).addTo(map);
            setTimeout(() => map.invalidateSize(), 150);
        }

        // ── Submit ────────────────────────────────────────────────────────────────
        window.submitAttendance = async function() {
            if (!currentCoords || submitting) return;
            submitting = true;

            const btn = document.getElementById('submitAttendanceBtn');
            const origText = document.getElementById('submitBtnText').textContent;
            btn.disabled = true;
            btn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving…`;

            try {
                const response = await fetch(config[currentMode].route, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        latitude: currentCoords.lat,
                        longitude: currentCoords.lon,
                        address: currentAddress,
                        accuracy: currentCoords.accuracy,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('attendanceModal')).hide();

                    // Update button state immediately without another server call
                    if (currentMode === 'checkin') {
                        activeLogId = data.log_id || null;
                        activeCheckinTime = data.checkin_at || new Date().toISOString();
                        setButtonState(true);
                        showToast(data.message || 'Checked in successfully!', 'success');
                    } else {
                        activeLogId = null;
                        activeCheckinTime = null;
                        setButtonState(false);
                        const dur = data.session_duration ? ` Duration: ${data.session_duration}` : '';
                        showToast((data.message || 'Checked out successfully!') + dur, 'success');
                    }
                } else {
                    showLocationError(data.message || 'Something went wrong. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML =
                        `<i data-feather="${config[currentMode].featherIcon}" style="width:14px;height:14px;"></i> <span>${origText}</span>`;
                    feather.replace();
                    submitting = false;
                }
            } catch (e) {
                showLocationError('Network error. Please check your connection and try again.');
                btn.disabled = false;
                btn.innerHTML =
                    `<i data-feather="${config[currentMode].featherIcon}" style="width:14px;height:14px;"></i> <span>${origText}</span>`;
                feather.replace();
                submitting = false;
            }
        };

        // ── Toast ─────────────────────────────────────────────────────────────────
        // function showToast(message, type) {
        //   const toastEl = document.getElementById('attendanceToast');
        //   const body    = document.getElementById('toastBody');
        //   toastEl.className = `toast align-items-center border-0 text-white bg-${type === 'success' ? 'success' : 'danger'}`;
        //   body.innerHTML    = `<i data-feather="check-circle" style="width:16px;height:16px;"></i> ${message}`;
        //   feather.replace();
        //   new bootstrap.Toast(toastEl, { delay: 3500 }).show();
        // }



        function showToast(message, type) {

            let toastInstance = null;

            function showToast(message, type = 'success') {

                const toastEl = document.getElementById('attendanceToast');
                const body = document.getElementById('toastBody');
                const closeBtn = document.getElementById('toastCloseBtn');


                // Remove old colors/classes
                toastEl.classList.remove(
                    'bg-success',
                    'bg-danger',
                    'text-white',
                    'show'
                );


                // Add new style
                if (type === 'success') {
                    toastEl.classList.add(
                        'bg-success',
                        'text-white'
                    );
                } else {
                    toastEl.classList.add(
                        'bg-danger',
                        'text-white'
                    );
                }


                body.innerHTML = `
        <i data-feather="${type === 'success' ? 'check-circle':'alert-circle'}"
           style="width:16px;height:16px;">
        </i>
        <span>${message}</span>
    `;


                feather.replace();


                // Create bootstrap toast instance
                toastInstance = bootstrap.Toast.getOrCreateInstance(
                    toastEl, {
                        delay: 4000,
                        autohide: true
                    }
                );


                // Manual close button always works
                closeBtn.onclick = function() {

                    toastInstance.hide();

                };


                toastInstance.show();

            }

        }

        // ── Cleanup on modal close ────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('attendanceModal').addEventListener('hidden.bs.modal', () => {
                if (map) {
                    map.remove();
                    map = null;
                    marker = null;
                }
                currentCoords = null;
                currentAddress = '';
            });

            // Load status from server on every page load
            loadAttendanceStatus();
        });
    })();
</script>
