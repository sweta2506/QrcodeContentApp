<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface 
{
    /**
     * @var App\Models\User
     */
    private $user;
    
    /**
     * Create a new user repository instance.
     *
     * @param  App\Models\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    /**
     * Create user in database
     *
     * @param array $userDetails
     * @return App\Models\User
     */
    public function createUser(array $userDetails): User
    {
        return $this->user->create($userDetails);
    }
}