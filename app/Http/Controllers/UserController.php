<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Concerns\ExportsDataTable;

class UserController extends Controller
{
    use ExportsDataTable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->applyDateFilters(User::select(['id', 'name', 'email', 'photo', 'created_at']), $request);
            
            return DataTables::of($users)
                ->editColumn('photo', function($user) {
                    if ($user->photo) {
                        return asset('storage/' . $user->photo);
                    }
                    return null;
                })
                ->editColumn('created_at', function($user) {
                    return $user->created_at->toISOString();
                })
                ->make(true);
        }

        return view('master-data.users.index');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->applyDateFilters(User::select(['id', 'name', 'email', 'photo', 'created_at']), $request);
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $rows = $query->orderBy('id')->get()->map(function($u) {
            return [
                'photo' => $u->photo ? storage_path('app/public/' . $u->photo) : null,
                'name' => $u->name,
                'email' => $u->email,
                'joined' => $u->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $headings = [
            'photo' => 'Photo',
            'name' => 'Name',
            'email' => 'Email',
            'joined' => 'Joined',
        ];

        return $this->exportWithImages($rows, $headings, 'photo', 'users_export');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-data.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
        
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('master-data.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('master-data.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Delete photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            
            $user->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
            }

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting user.'], 500);
            }

            return redirect()->route('users.index')->with('error', 'Error deleting user.');
        }
    }
}