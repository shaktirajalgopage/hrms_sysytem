<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\LoginActivityService;

class AuthController extends Controller
{
    public function login(
        Request $request,
        LoginActivityService $loginActivityService
    ) {

        if (empty($request->email)) {
            return response()->json([
                "message" => "please enter your email!"
            ], 422);
        }

        if (empty($request->password)) {
            return response()->json([
                "message" => "please enter your password"
            ], 422);
        }

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // MODIFIED: Added leftJoin to pull the role title dynamically
        $user = User::select('users.*', 'roles.title as role_name')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->where("email", $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            // Optional: store failed login attempt
            if ($user) {
                $loginActivityService->store(
                    $user,
                    $request,
                    'failed_login',
                    'failed',
                    'Sanctum API'
                );
            }

            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }

        // Create Sanctum Token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Store Login Activity
        $loginActivityService->store(
            $user,
            $request,
            'login',
            'success',
            'Sanctum API'
        );

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Login successful'
        ]);
    }

    public function logout(
        Request $request,
        LoginActivityService $loginActivityService
    ) {

        $user = $request->user();

        if ($user) {

            $loginActivityService->store(
                $user,
                $request,
                'logout',
                'success',
                'Sanctum API'
            );

            $request->user()->currentAccessToken()?->delete();
        }

        return response()->json([
            "message" => "Logged out successfully"
        ]);
    }

    /**
     * Get authenticated user profile along with corporate hierarchy names.
     * GET /api/v1/profile
     */
    /**
     * Get authenticated user profile with full avatar URLs and corporate hierarchies.
     * GET /api/v1/profile
     */
    public function profile(Request $request)
    {
        if (!empty($request->user())) {

            // Pre-calculate the base storage absolute path domain string matrix dynamically
            // Example: "http://yourdomain.com/storage/"
            $storageUrl = url('storage') . '/';

            $user = User::leftJoin(
                'employees',
                'employees.user_id',
                '=',
                'users.id'
            )
                ->leftJoin(
                    'departments',
                    'departments.id',
                    '=',
                    'employees.department_id'
                )
                ->leftJoin(
                    'designations',
                    'designations.id',
                    '=',
                    'employees.designation_id'
                )
                ->leftJoin(
                    'schedules',
                    'schedules.id',
                    '=',
                    'employees.schedule_id'
                )
                ->leftJoin(
                    'employee_hierarchies',
                    'employee_hierarchies.employee_id',
                    '=',
                    'employees.id'
                )
                ->leftJoin(
                    'employees as tl',
                    'tl.id',
                    '=',
                    'employee_hierarchies.team_lead_id'
                )
                ->leftJoin(
                    'employees as mgr',
                    'mgr.id',
                    '=',
                    'employee_hierarchies.manager_id'
                )
                ->leftJoin(
                    'employees as hr_emp',
                    'hr_emp.id',
                    '=',
                    'employee_hierarchies.hr_id'
                )
                ->where(
                    'users.email',
                    $request->user()->email
                )
                ->select(
                    'users.id',
                    'users.email',
                    'users.name',
                    'users.phone',
                    'employees.*',

                    // FIXED: Overwrites the raw employee avatar field string with an industry standard absolute download URL asset string
                    \DB::raw("CASE 
                        WHEN employees.avatar IS NOT NULL THEN CONCAT('{$storageUrl}', employees.avatar)
                        ELSE NULL 
                     END as avatar"),

                    'departments.title as department_name',
                    'designations.title as designation_name',
                    'schedules.title as schedule_name',

                    \DB::raw("CONCAT(tl.firstname, ' ', tl.lastname) as team_lead_name"),
                    \DB::raw("CONCAT(mgr.firstname, ' ', mgr.lastname) as manager_name"),
                    \DB::raw("CONCAT(hr_emp.firstname, ' ', hr_emp.lastname) as hr_name")
                )
                ->first();

            return response()->json([
                'user' => $user,
                'message' => 'user information found!'
            ]);
        }

        return response()->json([
            'message' => 'no user info available'
        ], 404);
    }


    /**
     * Retrieve all system user profiles accompanied by employee metrics.
     * GET /api/v1/all-employees
     */
    public function getAllEmployees(Request $request)
    { 
        // Get the authenticated user ID from the active Sanctum session token
        $currentUserId = $request->user()?->id;

        // Base Query Build
        $query = User::leftJoin(
            'employees',
            'employees.user_id',
            '=',
            'users.id'
        )
            ->leftJoin(
                'roles',
                'roles.id',
                '=',
                'users.role_id'
            );

        // 1. ENTERPRISE PARAMETER FILTER: Handle dynamic role filtration criteria dynamically
        if ($request->has('role') && !empty($request->role)) {
            $roleParam = $request->role;

            if (is_numeric($roleParam)) {
                $query->where('users.role_id', $roleParam);
            } else {
                $query->where('roles.title', $roleParam);
            }
        }

        // 2. SEARCH PARAMETER FILTER: Multi-column keyword matching wrapped in a logical grouping block
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;

            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('users.name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('users.email', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('users.phone', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('employees.firstname', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('employees.lastname', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('employees.unique_id', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Fetch records cleanly ordered
        $usersList = $query->select(
            'users.id as user_id',
            'users.name as user_display_name',
            'users.email',
            'users.phone',
            'users.role_id',
            'users.status as user_status',
            'roles.title as role_name',
            'employees.id as employee_id',
            'employees.unique_id as employee_code',
            'employees.firstname',
            'employees.lastname',
            'employees.emp_status'
        )
            ->orderBy('users.name', 'asc')
            ->get();

        // Mapping Data Array Structure Loop Engine
        $formattedUsersList = $usersList->map(function ($row) use ($currentUserId) {
            return [
                'user_id' => $row->user_id,
                'user_display_name' => $row->user_display_name,
                'email' => $row->email,
                'phone' => $row->phone,
                'role_id' => $row->role_id,
                'role_name' => $row->role_name ?? 'N/A',
                'employee_id' => $row->employee_id,
                'employee_code' => $row->employee_code,
                'firstname' => $row->firstname,
                'lastname' => $row->lastname,
                'emp_status' => $row->emp_status,
                'is_me' => $row->user_id === $currentUserId,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'All system user profiles compiled with identity mapping.',
            'filters_applied' => [
                'role' => $request->get('role') ?? 'none',
                'search' => $request->get('search') ?? 'none',
            ],
            'count' => $formattedUsersList->count(),
            'data' => $formattedUsersList
        ], 200);
    }
}
