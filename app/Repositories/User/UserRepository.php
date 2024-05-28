<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Repository;

class UserRepository extends Repository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->model = new User();
        $this->searchable = ['name', 'email'];
    }
}
