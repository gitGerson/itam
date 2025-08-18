<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'domain' => $request->domain,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['creator', 'updater', 'deleter']);
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

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

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

        return redirect()->route('users.trash')->with('success', 'User berhasil dipulihkan');
    }

    /**
     * Permanently delete user.
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
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
                return '
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . route('users.show', $user->id) . '">
                                <i class="bx bx-show me-1"></i> Lihat
                            </a>
                            <a class="dropdown-item" href="' . route('users.edit', $user->id) . '">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                            </a>
                            <form action="' . route('users.destroy', $user->id) . '" method="POST" style="display: inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus user ini?\')">
                                    <i class="bx bx-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                ';
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
                return '
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <form action="' . route('users.restore', $user->id) . '" method="POST" style="display: inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin memulihkan user ini?\')">
                                    <i class="bx bx-refresh me-1"></i> Pulihkan
                                </button>
                            </form>
                            <form action="' . route('users.force-delete', $user->id) . '" method="POST" style="display: inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus permanen user ini? Data tidak dapat dipulihkan!\')">
                                    <i class="bx bx-trash me-1"></i> Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
