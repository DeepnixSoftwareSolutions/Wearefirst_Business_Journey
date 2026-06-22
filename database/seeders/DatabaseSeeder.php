<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Node;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the Admin (The Company Owner)
        $admin = User::create([
            'member_id' => 1,
            'name' => 'Company Admin',
            'email' => 'wearefirstbusinessjourney22@gmail.com',
            'nic' => 'WBJADMIN0001',
            'phone' => '0700000000',
            'role' => 'Admin',
            'password' => Hash::make('AdminPass123'),
            'admission_status' => 'Active',
            'badge' => 'None',
            'middle_legs_unlocked' => true,
        ]);

        // 2. Create the Root Node for the Admin (NO SUB NODES)
        Node::create([
            'user_id' => $admin->id,
            'parent_node_id' => null, 
            'position' => null,
            'account_type' => 'Main'
        ]);

        // 3. Create the Operational Staff (Outside the tree)
        User::create([
            'name' => 'System Manager',
            'email' => 'manager@wearefirst.lk',
            'nic' => 'WBJMANAGER0001',
            'phone' => '0700000001',
            'role' => 'Manager',
            'password' => Hash::make('ManagerPass123'),
            'admission_status' => 'Active',
        ]);

        User::create([
            'name' => 'Accountant',
            'email' => 'hansifedo2000@gmail.com',
            'nic' => '200061202510',
            'phone' => '0700000002',
            'role' => 'Accountant',
            'password' => Hash::make('AccountantPass123'),
            'admission_status' => 'Active',
        ]);
        
        $this->command->info('System seeded! Admin root nodes and staff accounts created.');
    }
}
