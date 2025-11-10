<?php

namespace App\Repositories\User;

use App\Repositories\BaseInterface;
use App\User;

interface UserInterface extends BaseInterface
{
    public const ACCOUNT_STATUSES = [
        self::ACCOUNT_UNVERIFIED,
        self::ACCOUNT_DORMANT,
        self::ACCOUNT_VERIFIED,
        self::ACCOUNT_SUSPENDED,
        self::ACCOUNT_CLOSED
    ];
    public const ACCOUNT_UNVERIFIED = 'Unverified';
    public const ACCOUNT_DORMANT = 'Dormant';
    public const ACCOUNT_VERIFIED = 'Verified';
    public const ACCOUNT_SUSPENDED = 'Suspended';
    public const ACCOUNT_CLOSED = 'Closed';

    public function get($id, $with = '');
    public function getUserByAccountNumber($account_number);
    public function getAllUsers($account_status = null);
    public function update($id, $request);
    public function liftDormancy($user);
    public function updateAccountStatusDate(User $user, string $status): bool;
    public function appendKYCStatusToRemarks(User $user, string $status): bool;
    public function updateLastLogin(User $user): bool;
    public function updateKycStatus(int $id, array $request): User;
}
