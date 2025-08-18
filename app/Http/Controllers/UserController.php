<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Display a listing of soft deleted users.
     */
    public function trash()
    {
        return view('users.trash');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'domain' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'domain' => $request->domain,
        ]);

        UserLog::log(
            'CREATE_USER',
            "Created user: {$user->name} ({$user->username})",
            $user,
            null,
            $request->only(['name', 'username', 'email', 'domain'])
        );

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['creator', 'updater', 'deleter', 'roles']);
        
        UserLog::log(
            'VIEW_USER',
            "Viewed user: {$user->name} ({$user->username})",
            $user
        );
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'domain' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'domain' => $request->domain,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $oldValues = $user->only(['name', 'username', 'email', 'domain']);
        $user->update($updateData);

        UserLog::log(
            'UPDATE_USER',
            "Updated user: {$user->name} ({$user->username})",
            $user,
            $oldValues,
            $request->only(['name', 'username', 'email', 'domain'])
        );

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $userUsername = $user->username;
        
        $user->delete();

        UserLog::log(
            'DELETE_USER',
            "Soft deleted user: {$userName} ({$userUsername})",
            $user
        );

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    /**
     * Restore soft deleted user.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->deleted_by = null;
        $user->restore();

        UserLog::log(
            'RESTORE_USER',
            "Restored user: {$user->name} ({$user->username})",
            $user
        );

        return redirect()->route('users.trash')->with('success', 'User berhasil dipulihkan');
    }

    /**
     * Permanently delete user.
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $userName = $user->name;
        $userUsername = $user->username;
        
        UserLog::log(
            'FORCE_DELETE_USER',
            "Permanently deleted user: {$userName} ({$userUsername})",
            $user
        );
        
        $user->forceDelete();

        return redirect()->route('users.trash')->with('success', 'User berhasil dihapus permanen');
    }

    /**
     * Get data
     */
    public function getData(Request $request)
    {
        $users = User::with(['creator', 'updater'])
            ->select(['id', 'name', 'email', 'username', 'created_at', 'updated_at', 'created_by', 'updated_by']);

        return datatables()->of($users)
            ->addColumn('created_by_name', function ($user) {
                return $user->creator ? $user->creator->name : '-';
            })
            ->addColumn('updated_by_name', function ($user) {
                return $user->updater ? $user->updater->name : '-';
            })
            ->addColumn('action', function ($user) {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                // View action - always available if user has users.view permission
                if (auth()->user()->hasPermission('users.view')) {
                    $actions .= '<a class="dropdown-item" href="' . route('users.show', $user->id) . '">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                // Activity logs
                if (auth()->user()->hasPermission('users.logs')) {
                    $actions .= '<a class="dropdown-item" href="' . route('users.user-logs', $user->id) . '">
                        <i class="bx bx-history me-1"></i> Activity Log
                    </a>';
                }

                // Edit action
                if (auth()->user()->hasPermission('users.edit')) {
                    $actions .= '<a class="dropdown-item" href="' . route('users.edit', $user->id) . '">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                    $actions .= '<a class="dropdown-item" href="' . route('users.roles', $user->id) . '">
                        <i class="bx bx-shield me-1"></i> Kelola Role
                    </a>';
                }

                // Delete action
                if (auth()->user()->hasPermission('users.delete')) {
                    $actions .= '<form action="' . route('users.destroy', $user->id) . '" method="POST" style="display: inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus user ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                $actions .= '</div></div>';
                
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Get trash data
     */
    public function getTrashData(Request $request)
    {
        $users = User::onlyTrashed()
            ->with(['creator', 'updater', 'deleter'])
            ->select(['id', 'name', 'email', 'username', 'deleted_at', 'created_by', 'updated_by', 'deleted_by']);

        return datatables()->of($users)
            ->addColumn('created_by_name', function ($user) {
                return $user->creator ? $user->creator->name : '-';
            })
            ->addColumn('updated_by_name', function ($user) {
                return $user->updater ? $user->updater->name : '-';
            })
            ->addColumn('deleted_by_name', function ($user) {
                return $user->deleter ? $user->deleter->name : '-';
            })
            ->addColumn('action', function ($user) {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                // Restore action
                if (auth()->user()->hasPermission('users.restore')) {
                    $actions .= '<form action="' . route('users.restore', $user->id) . '" method="POST" style="display: inline;">
                        ' . csrf_field() . '
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin memulihkan user ini?\')">
                            <i class="bx bx-refresh me-1"></i> Pulihkan
                        </button>
                    </form>';
                }

                // Force delete action
                if (auth()->user()->hasPermission('users.force_delete')) {
                    $actions .= '<form action="' . route('users.force-delete', $user->id) . '" method="POST" style="display: inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus permanen user ini? Data tidak dapat dipulihkan!\')">
                            <i class="bx bx-trash me-1"></i> Hapus Permanen
                        </button>
                    </form>';
                }

                $actions .= '</div></div>';
                
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Display user logs
     */
    public function logs()
    {
        return view('users.logs');
    }

    /**
     * Get user logs data
     */
    public function getLogsData(Request $request)
    {
        $logs = UserLog::with(['user', 'targetUser'])
            ->select(['id', 'user_id', 'target_user_id', 'action', 'description', 'ip_address', 'created_at'])
            ->orderBy('created_at', 'desc');

        return datatables()->of($logs)
            ->addColumn('user_name', function ($log) {
                return $log->user ? $log->user->name : 'System';
            })
            ->addColumn('target_user_name', function ($log) {
                return $log->targetUser ? $log->targetUser->name : '-';
            })
            ->addColumn('formatted_date', function ($log) {
                return $log->created_at->format('d M Y H:i:s');
            })
            ->addColumn('action_badge', function ($log) {
                $badges = [
                    'CREATE_USER' => 'success',
                    'UPDATE_USER' => 'warning',
                    'DELETE_USER' => 'danger',
                    'RESTORE_USER' => 'info',
                    'FORCE_DELETE_USER' => 'dark',
                    'VIEW_USER' => 'secondary',
                ];
                $badgeClass = $badges[$log->action] ?? 'primary';
                return '<span class="badge bg-' . $badgeClass . '">' . str_replace('_', ' ', $log->action) . '</span>';
            })
            ->rawColumns(['action_badge'])
            ->make(true);
    }

    /**
     * Display user logs for specific user
     */
    public function userLogs(User $user)
    {
        return view('users.user-logs', compact('user'));
    }

    /**
     * Get user logs data for specific user
     */
    public function getUserLogsData(Request $request, User $user)
    {
        $logs = UserLog::with(['user'])
            ->where('target_user_id', $user->id)
            ->select(['id', 'user_id', 'action', 'description', 'ip_address', 'created_at'])
            ->orderBy('created_at', 'desc');

        return datatables()->of($logs)
            ->addColumn('user_name', function ($log) {
                return $log->user ? $log->user->name : 'System';
            })
            ->addColumn('formatted_date', function ($log) {
                return $log->created_at->format('d M Y H:i:s');
            })
            ->addColumn('action_badge', function ($log) {
                $badges = [
                    'CREATE_USER' => 'success',
                    'UPDATE_USER' => 'warning',
                    'DELETE_USER' => 'danger',
                    'RESTORE_USER' => 'info',
                    'FORCE_DELETE_USER' => 'dark',
                    'VIEW_USER' => 'secondary',
                ];
                $badgeClass = $badges[$log->action] ?? 'primary';
                return '<span class="badge bg-' . $badgeClass . '">' . str_replace('_', ' ', $log->action) . '</span>';
            })
            ->rawColumns(['action_badge'])
            ->make(true);
    }
}
