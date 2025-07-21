<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Namu\WireChat\Models\Conversation;

class AdminConversationSeeder extends Seeder
{
    public function run(): void
    {
        $systemUser = User::where('email', 'system@chocolatescm')->first();
        $admins = User::where('role', 'admin')->get();


        $conversation = $systemUser->createGroup(name: 'Admin Alerts');

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $conversation->addParticipant($admin);
        }

        $this->command->info('Admin Alerts group created successfully.');
    }
}
