<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * @return View
     */
    public function index()
    {
        $users = User::query()->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', [
            'roleOptions' => User::assignableRoles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(array_keys(User::assignableRoles()))],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('status', __('User account created.'));
    }

    /**
     * @return View
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roleOptions' => User::assignableRoles(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(array_keys(User::assignableRoles()))],
        ]);

        $this->ensureNotRemovingLastAdministrator($user, $validated['role']);

        // Keep self from locking everyone out accidentally.
        $actor = Auth::user();
        if ($actor instanceof User && $actor->id === $user->id && $validated['role'] !== User::ROLE_ADMIN) {
            $otherAdminExists = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->whereKeyNot($user->id)
                ->exists();

            if (! $otherAdminExists) {
                throw ValidationException::withMessages([
                    'role' => __('Create another Administrator account before demoting yours.'),
                ]);
            }
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);
        $user->save();

        return redirect()->route('users.index')->with('status', __('Account updated.'));
    }

    private function ensureNotRemovingLastAdministrator(User $subject, string $newRole): void
    {
        if ($subject->role !== User::ROLE_ADMIN || $newRole === User::ROLE_ADMIN) {
            return;
        }

        $otherAdminsExist = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->whereKeyNot($subject->id)
            ->exists();

        if (! $otherAdminsExist) {
            throw ValidationException::withMessages([
                'role' => __('At least one Administrator must remain.'),
            ]);
        }
    }
}
