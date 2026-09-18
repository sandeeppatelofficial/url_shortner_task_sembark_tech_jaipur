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

    public function test_admin_and_member_can_create_short_urls(): void
    {
        $company = Company::factory()->create();

        $admin = User::factory()->create(['company_id' => $company->id, 'role' => 'admin']);
        $member = User::factory()->create(['company_id' => $company->id, 'role' => 'member']);

        $this->actingAs($admin)
            ->post(route('urls.store'), ['original_url' => 'https://example.com/admin-link'])
            ->assertRedirect(route('urls.index'));

        $this->actingAs($member)
            ->post(route('urls.store'), ['original_url' => 'https://example.com/member-link'])
            ->assertRedirect(route('urls.index'));

        $this->assertDatabaseCount('short_urls', 2);
    }

    public function test_superadmin_cannot_create_short_urls(): void
    {
        $superadmin = User::factory()->create(['company_id' => null, 'role' => 'superadmin']);

        $this->actingAs($superadmin)
            ->post(route('urls.store'), ['original_url' => 'https://example.com/nope'])
            ->assertForbidden();

        $this->actingAs($superadmin)
            ->get(route('urls.create'))
            ->assertForbidden();

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_admin_only_sees_short_urls_from_their_own_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $adminA = User::factory()->create(['company_id' => $companyA->id, 'role' => 'admin']);
        $userA2 = User::factory()->create(['company_id' => $companyA->id, 'role' => 'member']);
        $adminB = User::factory()->create(['company_id' => $companyB->id, 'role' => 'admin']);

        ShortUrl::factory()->create(['company_id' => $companyA->id, 'user_id' => $adminA->id]);
        ShortUrl::factory()->create(['company_id' => $companyA->id, 'user_id' => $userA2->id]);
        ShortUrl::factory()->create(['company_id' => $companyB->id, 'user_id' => $adminB->id]);

        $response = $this->actingAs($adminA)->get(route('urls.index'));

        $response->assertOk();
        $response->assertViewHas('shortUrls', function ($shortUrls) {
            return $shortUrls->total() === 2;
        });
    }

    public function test_member_only_sees_short_urls_created_by_themselves(): void
    {
        $company = Company::factory()->create();

        $memberOne = User::factory()->create(['company_id' => $company->id, 'role' => 'member']);
        $memberTwo = User::factory()->create(['company_id' => $company->id, 'role' => 'member']);

        ShortUrl::factory()->create(['company_id' => $company->id, 'user_id' => $memberOne->id]);
        ShortUrl::factory()->create(['company_id' => $company->id, 'user_id' => $memberTwo->id]);

        $response = $this->actingAs($memberOne)->get(route('urls.index'));

        $response->assertOk();
        $response->assertViewHas('shortUrls', function ($shortUrls) {
            return $shortUrls->total() === 1;
        });
    }

    public function test_superadmin_sees_short_urls_from_every_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $userA = User::factory()->create(['company_id' => $companyA->id, 'role' => 'member']);
        $userB = User::factory()->create(['company_id' => $companyB->id, 'role' => 'member']);
        $superadmin = User::factory()->create(['company_id' => null, 'role' => 'superadmin']);

        ShortUrl::factory()->create(['company_id' => $companyA->id, 'user_id' => $userA->id]);
        ShortUrl::factory()->create(['company_id' => $companyB->id, 'user_id' => $userB->id]);

        $response = $this->actingAs($superadmin)->get(route('urls.index'));

        $response->assertOk();
        $response->assertViewHas('shortUrls', function ($shortUrls) {
            return $shortUrls->total() === 2;
        });
    }

    public function test_short_urls_are_publicly_resolvable(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id, 'role' => 'member']);

        $shortUrl = ShortUrl::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'original_url' => 'https://example.com/target-page',
            'short_code' => 'abc123',
        ]);

        $this->get('/s/abc123')->assertRedirect('https://example.com/target-page');
    }
}
