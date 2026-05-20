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
        return Socialite::driver('github')->stateless()->redirect();
    }

    public function callback()
    {
        $githubUser = Socialite::driver('github')->stateless()->user();

        $user = User::where('email', $githubUser->getEmail())->first();

        if ($user) {
            $user->update([
                'github_id' => $githubUser->getId(),
                'name' => $githubUser->getName() ?: $githubUser->getNickname(),
            ]);
        } else {
            $user = User::create([
                'github_id' => $githubUser->getId(),
                'name' => $githubUser->getName() ?: $githubUser->getNickname(),
                'email' => $githubUser->getEmail(),
                'password' => Hash::make(uniqid()),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('posts.index');
    }
}
