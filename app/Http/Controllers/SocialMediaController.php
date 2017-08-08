<?php

namespace App\Http\Controllers;

use App\User;
use Socialite;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
    	return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
    	$socialMediaUser = Socialite::driver($provider)->user();
    	
    	$user = $this->findOrCreateUser($socialMediaUser);

    	auth()->login($user);

    	return redirect()->route('home');
    }

	/**
     * Obtain the user information from GitHub.
     *
     * @param Laravel\Socialite\Two\User $socialMediaUser
     * @return App\User $user
     */
    public function findOrCreateUser($socialMediaUser)
    {
    	$user = User::where('social_provider_id', $socialMediaUser->getId())->first();
    	
    	if(is_null($user)) {
    		$user = User::create([
    			'social_provider_id' => $socialMediaUser->getId(),
    			'name' => $socialMediaUser->getName(),
    			'email' => $socialMediaUser->getEmail(),
    			'avatar' => $socialMediaUser->getAvatar()
			]);
    	}
    	
    	return $user;
    }
}
