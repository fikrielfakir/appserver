<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $existingAdmin = AdminUser::where('username', 'admin')->first();
        
        if (!$existingAdmin) {
            AdminUser::create([
                'id' => (string) Str::uuid(),
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'role' => 'super_admin',
            ]);
            
            $this->command->info('Default admin user created: admin / admin123');
        } else {
            $this->command->info('Admin user already exists');
        }
    }
}
