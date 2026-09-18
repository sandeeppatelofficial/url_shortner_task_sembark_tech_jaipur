<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(): View
    {
        return view('invitations.create');
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $user = $request->user();

        $company = $user->isSuperAdmin()
            ? Company::create(['name' => $request->validated('company_name')])
            : $user->company;

        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $user->id,
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'token' => Str::random(40),
        ]);

        return redirect()->route('invitations.create')->with([
            'status' => 'Invitation created successfully.',
            'invite_link' => route('invitations.accept', $invitation->token),
        ]);
    }

    public function accept(string $token): View
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        return view('auth.register', ['invitation' => $invitation]);
    }

    public function complete(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'company_id' => $invitation->company_id,
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
            'role' => $invitation->role,
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('urls.index')->with('status', 'Welcome! Your account is ready.');
    }
}
