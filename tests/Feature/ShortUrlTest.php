<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_and_admin_can_create_short_urls(): void
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'email' => 'contact@acme.test',
        ]);

        $admin = User::factory()->create([
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $member = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        $adminResponse = $this->actingAs($admin)->post(route('admin.short_urls.add'), [
            'original_url' => 'https://sembark.com/features',
        ]);
        $adminResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('short_urls', [
            'user_id' => $admin->id,
            'company_id' => $company->id,
            'original_url' => 'https://sembark.com/features',
        ]);

        $memberResponse = $this->actingAs($member)->post(route('member.short_urls.add'), [
            'original_url' => 'https://sembark.com/pricing',
        ]);
        $memberResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('short_urls', [
            'user_id' => $member->id,
            'company_id' => $company->id,
            'original_url' => 'https://sembark.com/pricing',
        ]);
    }

    public function test_superadmin_cannot_create_short_urls(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        $response = $this->actingAs($superadmin)->post(route('admin.short_urls.add'), [
            'original_url' => 'https://sembark.com',
        ]);

        $response->assertRedirect(route('superadmin.dashboard'));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('short_urls', [
            'original_url' => 'https://sembark.com',
        ]);
    }

    public function test_admin_can_only_see_short_urls_created_in_their_own_company(): void
    {
        $companyA = Company::create(['name' => 'Company A', 'email' => 'a@test.com']);
        $companyB = Company::create(['name' => 'Company B', 'email' => 'b@test.com']);

        $adminA = User::factory()->create(['role' => 'Admin', 'company_id' => $companyA->id]);
        $adminB = User::factory()->create(['role' => 'Admin', 'company_id' => $companyB->id]);

        $urlA = ShortUrl::create([
            'company_id' => $companyA->id,
            'user_id' => $adminA->id,
            'original_url' => 'https://sembark.com/company-a',
            'short_code' => 'compA1',
            'clicks' => 0,
        ]);

        $urlB = ShortUrl::create([
            'company_id' => $companyB->id,
            'user_id' => $adminB->id,
            'original_url' => 'https://sembark.com/company-b',
            'short_code' => 'compB1',
            'clicks' => 0,
        ]);

        $response = $this->actingAs($adminA)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('compA1');
        $response->assertDontSee('compB1');
    }

    public function test_member_can_only_see_short_urls_created_by_themselves(): void
    {
        $company = Company::create(['name' => 'Sembark', 'email' => 'team@sembark.com']);

        $member1 = User::factory()->create(['role' => 'Member', 'company_id' => $company->id]);
        $member2 = User::factory()->create(['role' => 'Member', 'company_id' => $company->id]);

        $url1 = ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $member1->id,
            'original_url' => 'https://sembark.com/member-one',
            'short_code' => 'mem1xx',
            'clicks' => 0,
        ]);

        $url2 = ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $member2->id,
            'original_url' => 'https://sembark.com/member-two',
            'short_code' => 'mem2yy',
            'clicks' => 0,
        ]);

        $response = $this->actingAs($member1)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('member.dashboard'));

        $response->assertOk();
        $response->assertSee('mem1xx');
        $response->assertDontSee('mem2yy');
    }

    public function test_short_urls_are_publicly_resolvable_and_redirect_to_original_url(): void
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'email' => 'contact@acme.test',
        ]);

        $user = User::factory()->create([
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        $shortUrl = ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'original_url' => 'https://sembark.com/blog',
            'short_code' => 'xyz123',
            'clicks' => 0,
        ]);

        $response = $this->get('/s/xyz123');

        $response->assertRedirect('https://sembark.com/blog');

        $this->assertDatabaseHas('short_urls', [
            'id' => $shortUrl->id,
            'clicks' => 1,
        ]);
    }

    public function test_invalid_short_code_redirects_with_error(): void
    {
        $response = $this->get('/s/invalid999');

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }
}
