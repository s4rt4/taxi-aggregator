<?php

namespace Database\Seeders;

use App\Enums\AdminPermission;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'is_system' => true,
                'description' => 'Full access to everything. Can manage other admins and roles.',
                'permissions' => null, // null = all permissions
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'is_system' => true,
                'description' => 'Manage operators, bookings, disputes, users. Cannot change settings or manage admins.',
                'permissions' => AdminPermission::ADMIN,
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'is_system' => true,
                'description' => 'Revenue dashboard, statements, payouts only.',
                'permissions' => AdminPermission::FINANCE,
            ],
            [
                'name' => 'Support',
                'slug' => 'support',
                'is_system' => true,
                'description' => 'Bookings, disputes, trip issues support.',
                'permissions' => AdminPermission::SUPPORT,
            ],
        ];

        foreach ($roles as $role) {
            AdminRole::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }

        // Create test admin users for each role
        $superAdmin = User::firstOrCreate(['email' => 'superadmin@test.com'], [
            'name' => 'Super Admin',
            'role' => 'admin',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);
        $superAdmin->update(['admin_role_id' => AdminRole::where('slug', 'super-admin')->first()->id]);

        // Update existing admin@test.com to use admin role (not super-admin)
        $admin = User::firstOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Admin User',
            'role' => 'admin',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);
        $admin->update([
            'password' => 'password123',
            'admin_role_id' => AdminRole::where('slug', 'admin')->first()->id,
        ]);

        $finance = User::firstOrCreate(['email' => 'finance@test.com'], [
            'name' => 'Finance User',
            'role' => 'admin',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);
        $finance->update(['admin_role_id' => AdminRole::where('slug', 'finance')->first()->id]);

        $support = User::firstOrCreate(['email' => 'support@test.com'], [
            'name' => 'Support User',
            'role' => 'admin',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);
        $support->update(['admin_role_id' => AdminRole::where('slug', 'support')->first()->id]);

        // Update operator and passenger test passwords
        User::where('email', 'operator@test.com')->update(['password' => 'password123']);
        User::where('email', 'passenger@test.com')->update(['password' => 'password123']);

        // Corporate super user
        $corpUser = User::firstOrCreate(['email' => 'corporate@test.com'], [
            'name' => 'Corporate Admin',
            'role' => 'corporate',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);
        $corpUser->update(['password' => 'password123']);

        $corporate = \App\Models\Corporate::firstOrCreate(
            ['billing_email' => 'corporate@test.com'],
            [
                'company_name' => 'Test Corporation Ltd',
                'legal_name' => 'Test Corporation Limited',
                'business_type' => 'limited_company',
                'billing_phone' => '+44 20 1234 5678',
                'billing_address_line_1' => '1 Corporate Plaza',
                'billing_city' => 'London',
                'billing_postcode' => 'EC1A 1BB',
                'monthly_budget' => 10000,
                'invoicing_frequency' => 'monthly',
                'payment_terms_days' => 14,
                'status' => 'approved',
                'approved_at' => now(),
            ]
        );

        \App\Models\CorporateUser::firstOrCreate(
            [
                'corporate_id' => $corporate->id,
                'user_id' => $corpUser->id,
            ],
            [
                'corporate_role' => 'super_user',
                'is_active' => true,
            ]
        );
    }
}
