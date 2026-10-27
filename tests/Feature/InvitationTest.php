<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_invite_client(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        $response = $this->actingAs($superadmin)->post(route('superadmin.clients.add'), [
            'company_name' => 'New Client Ltd',
            'email' => 'clientadmin@newclient.com',
        ]);

        $response->assertSessionHas('invitation_url');
        $this->assertDatabaseHas('companies', [
            'name' => 'New Client Ltd',
            'email' => 'clientadmin@newclient.com',
        ]);
        $this->assertDatabaseHas('invitations', [
            'email' => 'clientadmin@newclient.com',
            'role' => 'Admin',
        ]);
    }

    public function test_admin_can_invite_team_member(): void
    {
        $company = Company::create(['name' => 'Sembark Tech', 'email' => 'info@sembark.com']);
        $admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.members.add'), [
            'email' => 'newdev@sembark.com',
            'role' => 'Member',
        ]);

        $response->assertSessionHas('invitation_url');
        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'newdev@sembark.com',
            'role' => 'Member',
        ]);
    }

    public function test_invited_user_can_accept_invitation_and_activate_account(): void
    {
        $company = Company::create(['name' => 'Sembark Tech', 'email' => 'info@sembark.com']);
        $admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $admin->id,
            'email' => 'alice@sembark.com',
            'role' => 'Member',
            'token' => 'testtoken12345',
            'expires_at' => now()->addDays(7),
        ]);

        $viewResponse = $this->get(route('invitations.show', 'testtoken12345'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('alice@sembark.com');

        $acceptResponse = $this->post(route('invitations.accept', 'testtoken12345'), [
            'name' => 'Alice Smith',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $acceptResponse->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Alice Smith',
            'email' => 'alice@sembark.com',
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }
}
