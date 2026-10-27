<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = auth()->user();

            $request->validate([
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'role' => ['required', 'in:Admin,Member'],
            ]);

            $invitation = Invitation::create([
                'company_id' => $user->company_id,
                'invited_by' => $user->id,
                'email' => $request->email,
                'role' => $request->role,
                'token' => Str::random(40),
                'expires_at' => now()->addDays(7),
            ]);

            $inviteUrl = route('invitations.show', $invitation->token);

            return redirect()->route('admin.dashboard')
                ->with('invitation_url', $inviteUrl)
                ->with('success', 'Invitation generated! Link: ' . $inviteUrl);
        }

        return redirect()->back()->with('error', 'Invalid request.');
    }
}
