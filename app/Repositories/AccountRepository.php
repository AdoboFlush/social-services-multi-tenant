<?php

namespace App\Repositories;

use App\Account;


/** 
* Class AccountRepository
* 
* @package App\Repositories
*/
class AccountRepository 
{

    /**
    * @var $model
    */
    protected $model;

    /**
    * AccountRepository constructor.
    *
    * @param Account $account
    */
    public function __construct(Account $account)
    {
        $this->model = $account;
    }
}