<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $users = User::factory(4)->create();
        $allUsers = User::query()->get();

        Post::factory(10)->create([
            'user_id' => $testUser->id,
        ]);

        Post::factory(10)->create([
            'user_id' => fn () => $allUsers->random()->id,
        ]);

        $posts = Post::all();

        Comment::factory(25)->create([
            'post_id' => fn () => $posts->random()->id,
            'user_id' => fn () => $allUsers->random()->id,
        ]);
    }
}
