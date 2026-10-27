<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class MemberExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_export_their_short_urls(): void
    {
        Excel::fake();

        $company = Company::create(['name' => 'Acme Inc', 'email' => 'acme@test.com']);
        $member = User::factory()->create(['role' => 'Member', 'company_id' => $company->id]);

        $response = $this->actingAs($member)->get(route('member.export'));

        $response->assertOk();
        Excel::assertDownloaded('urls_' . date('Y-m-d') . '.csv');
    }

    public function test_admin_can_export_company_short_urls(): void
    {
        Excel::fake();

        $company = Company::create(['name' => 'Acme Inc', 'email' => 'acme@test.com']);
        $admin = User::factory()->create(['role' => 'Admin', 'company_id' => $company->id]);

        $response = $this->actingAs($admin)->get(route('admin.export'));

        $response->assertOk();
        Excel::assertDownloaded('urls_' . date('Y-m-d') . '.csv');
    }

    public function test_superadmin_can_export_all_short_urls(): void
    {
        Excel::fake();

        $superadmin = User::factory()->create(['role' => 'SuperAdmin']);

        $response = $this->actingAs($superadmin)->get(route('superadmin.export'));

        $response->assertOk();
        Excel::assertDownloaded('urls_' . date('Y-m-d') . '.csv');
    }
}
