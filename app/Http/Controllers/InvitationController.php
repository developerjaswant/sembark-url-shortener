<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{
    public function show(string $token)
    {
        $invitation = Invitation::with('company')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect('/login')->with('error', 'This invitation link is invalid or has expired.');
        }

        return view('invitations.accept', compact('invitation'));
    }

    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect('/login')->with('error', 'This invitation link is invalid or has expired.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (User::where('email', $invitation->email)->exists()) {
            return redirect('/login')->with('error', 'An account with this email already exists. Please log in.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
            'company_id' => $invitation->company_id,
            'role' => $invitation->role,
        ]);

        $invitation->update([
            'accepted_at' => now(),
        ]);

        auth()->login($user);

        if ($user->role === 'Admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome! Your account is active.');
        }

        return redirect()->route('member.dashboard')->with('success', 'Welcome! Your account is active.');
    }
}
