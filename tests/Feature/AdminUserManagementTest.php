<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'role'     => 'administrator',
            'password' => Hash::make('password'),
        ]);

        $this->operator = User::create([
            'name'       => 'Operator Test',
            'email'      => 'operator@test.com',
            'role'       => 'operator',
            'perusahaan' => 'Avsec Bandara',
            'password'   => Hash::make('password'),
        ]);
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get(route('administrator.users.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('administrator.users.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_operator_cannot_access_admin_user_management(): void
    {
        $response = $this->actingAs($this->operator)->get(route('administrator.users.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_user_management_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Akun Operator');
        $response->assertSee('+ Tambah Akun Operator');
        $response->assertSee('Operator Test');
        $response->assertSee('operator@test.com');
        $response->assertSee('Admin Test');
    }

    public function test_admin_can_filter_and_search_users(): void
    {
        // Filter by role operator
        $response = $this->actingAs($this->admin)->get(route('administrator.users.index', ['role' => 'operator']));
        $response->assertStatus(200);
        $response->assertSee('Operator Test');

        // Search keyword
        $response = $this->actingAs($this->admin)->get(route('administrator.users.index', ['search' => 'Avsec']));
        $response->assertStatus(200);
        $response->assertSee('Operator Test');
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.users.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Akun Operator / User');
        $response->assertSee('Simpan Akun');
    }

    public function test_admin_can_create_new_operator_account(): void
    {
        $userData = [
            'name'                  => 'Budi Operator Baru',
            'email'                 => 'budi.operator@bandara.test',
            'role'                  => 'operator',
            'perusahaan'            => 'PT Angkasa Pura Support',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($this->admin)->post(route('administrator.users.store'), $userData);

        $response->assertRedirect(route('administrator.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name'       => 'Budi Operator Baru',
            'email'      => 'budi.operator@bandara.test',
            'role'       => 'operator',
            'perusahaan' => 'PT Angkasa Pura Support',
        ]);

        $createdUser = User::where('email', 'budi.operator@bandara.test')->first();
        $this->assertTrue(Hash::check('password123', $createdUser->password));
    }

    public function test_create_user_validation_fails_for_invalid_data(): void
    {
        // Existing email
        $response = $this->actingAs($this->admin)->post(route('administrator.users.store'), [
            'name'                  => 'Duplikat User',
            'email'                 => 'operator@test.com',
            'role'                  => 'operator',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');

        // Unmatched password confirmation
        $response = $this->actingAs($this->admin)->post(route('administrator.users.store'), [
            'name'                  => 'Mismatch User',
            'email'                 => 'mismatch@bandara.test',
            'role'                  => 'operator',
            'password'              => 'password123',
            'password_confirmation' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_admin_can_view_edit_user_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.users.edit', $this->operator->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Akun: Operator Test');
        $response->assertSee('operator@test.com');
    }

    public function test_admin_can_update_operator_account(): void
    {
        $updateData = [
            'name'       => 'Operator Nama Baru',
            'email'      => 'operator.updated@test.com',
            'role'       => 'operator',
            'perusahaan' => 'Divisi Keamanan Khusus',
        ];

        $response = $this->actingAs($this->admin)->put(route('administrator.users.update', $this->operator->id), $updateData);

        $response->assertRedirect(route('administrator.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'         => $this->operator->id,
            'name'       => 'Operator Nama Baru',
            'email'      => 'operator.updated@test.com',
            'perusahaan' => 'Divisi Keamanan Khusus',
        ]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('administrator.users.destroy', $this->admin->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_operator_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('administrator.users.destroy', $this->operator->id));

        $response->assertRedirect(route('administrator.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $this->operator->id]);
    }

    public function test_login_page_does_not_contain_scan_qr_tab(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertDontSee('Scan QR');
        $response->assertSee('MASUK KE SISTEM');
    }

    public function test_spa_ajax_request_returns_users_table_partial(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.users.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_X-SPA'            => 'true',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('administrator.users.partials.table');
        $response->assertSee('Operator Test');
        $response->assertSee('Admin Test');
        $response->assertSee('spaKpiData');
    }
}
