<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'company_name' => ['required', 'unique:companies,name'],
                'email' => ['required', 'email', 'unique:users,email'],
                'user_name' => ['nullable', 'string', 'max:255'],
            ]);

            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->email,
            ]);

            $invitation = Invitation::create([
                'company_id' => $company->id,
                'invited_by' => auth()->id(),
                'email' => $request->email,
                'role' => 'Admin',
                'token' => Str::random(40),
                'expires_at' => now()->addDays(7),
            ]);

            $inviteUrl = route('invitations.show', $invitation->token);

            return redirect()->route('superadmin.dashboard')
                ->with('invitation_url', $inviteUrl)
                ->with('success', 'Company created!');
        }

        return view('superadmin.invite');
    }
}
