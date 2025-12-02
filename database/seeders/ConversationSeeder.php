<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Conversation::factory()
            ->count(2)
            ->hasMessages(5)
            ->create([
                'user_id' => 1,
            ]);
    }
}
