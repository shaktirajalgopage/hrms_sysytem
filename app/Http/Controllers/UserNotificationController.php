<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Models\UserAppNotificastion;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Role;

class UserNotificationController extends Controller
{


private function routePrefix()
{
    return auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
}
    public function __construct(private NotificationService $service) {}

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $notifications = UserAppNotificastion::with('creator')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'     => UserAppNotificastion::count(),
            'sent'      => UserAppNotificastion::where('status', 'sent')->count(),
            'draft'     => UserAppNotificastion::where('status', 'draft')->count(),
            'scheduled' => UserAppNotificastion::where('status', 'scheduled')->count(),
        ];

        return view('admin.user-notifications.index', [
            'notifications' => $notifications,
            'categories'    => UserAppNotificastion::categoryList(),
            'stats'         => $stats,
        ]);
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create(): View
    {
        $roles     = Role::pluck('slug');
        $employees = User::with('employee')->get()->mapWithKeys(fn($u) => [
            $u->id => ($u->employee->firstname ?? '') . ' ' . ($u->employee->lastname ?? '') . " (#{$u->id})",
        ]);

        return view('admin.user-notifications.create', [
            'categories' => UserAppNotificastion::categoryList(),
            'types'      => UserAppNotificastion::typeList(),
            'roles'      => $roles,
            'employees'  => $employees,
        ]);
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(StoreNotificationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        if ($request->action === 'send') {
            $result = $this->service->createAndSend($data, auth()->id());
            $msg    = $result['success']
                ? "✅ {$result['message']}"
                : "❌ {$result['message']}";

            return redirect()->route($this->routePrefix() . 'user-notifications.index')
                             ->with($result['success'] ? 'success' : 'error', $msg);
        }

        if ($request->action === 'schedule' && !empty($data['scheduled_at'])) {
            $this->service->schedule($data, auth()->id());
            return redirect()->route($this->routePrefix() . 'user-notifications.index')
                             ->with('success', '📅 Notification scheduled successfully.');
        }

        // Save as draft
        $data['status'] = 'draft';
        UserAppNotificastion::create($data);

        return redirect()->route($this->routePrefix() . 'user-notifications.index')
                         ->with('success', '💾 Notification saved as draft.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(UserAppNotificastion $notification): View
    {
        $notification->load(['creator', 'recipients.user.employee']);

        $readCount      = $notification->read_count;
        $totalCount     = $notification->total_recipients_count;
        $unreadCount    = $totalCount - $readCount;
        $readPercentage = $totalCount > 0 ? round(($readCount / $totalCount) * 100) : 0;

        return view('admin.user-notifications.show', compact(
            'notification', 'readCount', 'totalCount', 'unreadCount', 'readPercentage'
        ));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(UserAppNotificastion $notification): View
    {
        abort_if($notification->status === 'sent', 403, 'Cannot edit a sent notification.');

        $roles     = \App\Models\Role::pluck('name', 'slug');
        $employees = User::with('employee')->get()->mapWithKeys(fn($u) => [
            $u->id => ($u->employee->firstname ?? '') . ' ' . ($u->employee->lastname ?? '') . " (#{$u->id})",
        ]);

        return view('admin.user-notifications.edit', [
            'notification' => $notification,
            'categories'   => UserAppNotificastion::categoryList(),
            'types'        => UserAppNotificastion::typeList(),
            'roles'        => $roles,
            'employees'    => $employees,
        ]);
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(UpdateNotificationRequest $request, UserAppNotificastion $notification): RedirectResponse
    {
        abort_if($notification->status === 'sent', 403, 'Cannot edit a sent notification.');

        $notification->update($request->validated());

        return redirect()->route($this->routePrefix() . ' user-notifications.index')
                         ->with('success', 'Notification updated successfully.');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(UserAppNotificastion $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('user-notifications.index')
                         ->with('success', 'Notification deleted.');
    }

    // ─── Send Now (from draft) ────────────────────────────────────────────────

    public function sendNow(UserAppNotificastion $notification): RedirectResponse
    {
        abort_if($notification->status === 'sent', 403, 'Already sent.');

        $result = $this->service->dispatch($notification);

        return redirect()->route('user-notifications.show', $notification)
                         ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    // ─── Duplicate ────────────────────────────────────────────────────────────

    public function duplicate(UserAppNotificastion $notification): RedirectResponse
    {
        $new = $notification->replicate();
        // dd($new);
        $new->status      = 'draft';
        $new->sent_at     = null;
        $new->title       = '[Copy] ' . $notification->title;
        $new->body = $notification->body;
        $new->created_by  = auth()->id();
        $new->save();

        return redirect()->route('user-notifications.edit', $new)
                         ->with('success', 'Notification duplicated. Edit and send when ready.');
    }
}