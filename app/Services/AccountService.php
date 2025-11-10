<?php

namespace App\Services;

use App\Repositories\UserRepository;

/**
 * Class AccountService
 */
class AccountService extends BaseService
{
    /**
     * @var $repository
     */
    protected $repository;

    /**
     * @var $min
     */
    private $min = 10000000;

    /**
     * @var $max
     */
    private $max = 99999999;

    /**
     * AccountService constructor.
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->repository = $userRepository;
    }

    /**
     * Get account by accountNumber.
     *
     *
     * @return Account
     */
    public function generateAccountNumber()
    {
        $accountNumberPrefix = 'AA-';
        $accountNumber = $accountNumberPrefix . random_int($this->min,$this->max);
        if($this->repository->checkAccountNumberIfExists($accountNumber)) {
            $this->generateAccountNumber();
        } else {
            return $accountNumber;
        }
    }


}