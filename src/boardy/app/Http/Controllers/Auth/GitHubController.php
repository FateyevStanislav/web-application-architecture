<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::where('github_id', $githubUser->getId())->first();

        if (! $user && $githubUser->getEmail()) {
            $user = User::where('email', $githubUser->getEmail())->first();
        }

        if ($user) {
            $user->update([
                'github_id' => $githubUser->getId(),
                'name' => $user->name ?: ($githubUser->getName() ?: $githubUser->getNickname()),
                'email' => $user->email ?: $githubUser->getEmail(),
            ]);
        } else {
            $user = User::create([
                'github_id' => $githubUser->getId(),
                'name' => $githubUser->getName() ?: $githubUser->getNickname() ?: 'GitHub User',
                'email' => $githubUser->getEmail(),
                'password' => Hash::make(str()->random(32)),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('posts.index');
    }
}
