<?php
namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface 
{
    /**
     * Create user in database
     *
     * @param array $userDetails
     * @return App\Models\User
     */
    public function createUser(array $userDetails): User;
}