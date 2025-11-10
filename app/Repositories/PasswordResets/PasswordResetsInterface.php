<?php

namespace App\Repositories\PasswordResets;

use App\Repositories\BaseInterface;

interface PasswordResetsInterface extends BaseInterface
{
    public function findByEmail($email);
}
