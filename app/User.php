<?php

namespace App;

use App\Repositories\User\UserInterface;
use App\Traits\ActivityLog\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use Notifiable, HasRoles, LogsActivity, SoftDeletes;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_number',
        'account_type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'user_type',
        'user_access',
        'mid',
        'profile_picture',
        'business_name',
        'status',
        'account_status',
        'kyc_status',
        'kyc_status_updated_at',
        'kyc_remarks',
        'fee_remarks',
        'email_verified_at',
        'refer_user_id',
        'referral_switch',
        'verification_code',
        'verification_code_expires_at',
        'document_submitted_at',
        'card_application_exemption',
        'card_register_status',
        'card_number',
        'card_register_at',
        'is_dormant',
        'is_admin_account',
        'disable_jp_deposit_fee',
        'change_password',
        'created_by',
        'updated_by',
        'access_type',
        'full_name',
        'is_included_on_dormancy',
        'newsletter',
        'restriction_properties',
    ];

    protected $appends = ['is_admin', 'is_member', 'is_suspended', 'full_name', 'is_personal'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static $logName = 'User';

    protected static $ignoreChangedAttributes = [
        'updated_by',
        'created_at',
        'updated_at',
        'remember_token',
        'kyc_status_updated_at',
        'document_submitted_at'
    ];

    protected static $logAttributes = ["*"];

    protected static $logOnlyDirty = true;

    protected static $recordEvents = ['updated'];

    public const ADMIN = 'admin';
    public const MEMBER = 'member';
    public const TAGGER = 'tagger';
    public const PAYMASTER = 'paymaster';
    public const WATCHER = 'watcher';
    public const RECEIVE_NEWSLETTER_UPDATES = 1;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function affiliate_details()
    {
        return $this->hasOne('\App\AffiliateDetails', 'user_id');
    }

    public function documents()
    {
        return $this->hasMany('App\Document', 'user_id');
    }

    public function user_information()
    {
        return $this->hasOne('App\UserInformation', 'user_id')->withDefault();
    }

    public function accounts()
    {
        return $this->hasMany('App\Account', 'user_id');
    }

    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function members()
    {
        return $this->hasMany('App\User', 'refer_user_id');
    }

    public function security()
    {
        return $this->hasOne('App\Security', 'user_id')->withDefault();
    }

    public function encoded_social_service_assistance()
    {
        return $this->hasMany('App\SocialServiceAssistance', 'encoder_id');
    }

    public function approved_social_service_assistance()
    {
        return $this->hasMany('App\SocialServiceAssistance', 'approved_by');
    }

    public function isKycExempted(): bool
    {
        return (isset($this->affiliate_details) &&
            isset($this->affiliate_details->parent) &&
            $this->affiliate_details->parent->kyc_privilege_switch);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $userDocumentAttr = array('kyc_status', 'kyc_remarks');
        $changes = $activity->changes();
        foreach ($changes['attributes'] as $key => $value) {
            if (in_array($key, $userDocumentAttr)) {
                $activity->log_name = "User Document";
                break;
            }
        }

        if (isset($activity->subject) && $activity->subject->user_type == "admin") {
            $oldProps = $activity->properties["old"];
            $oldProps["password"] = "";
            $attributes = $activity->properties["attributes"];
            $attributes["password"] = "";
            $activity->properties = array("old" => $oldProps, "attributes" => $attributes);
            $activity->log_name = "Manager and Admin";
        }
    }

    public function generateTwoFactorCode($minutes = 15)
    {
        $this->timestamps = false;
        $this->verification_code = rand(100000, 999999);
        $this->verification_code_expires_at = now()->addMinutes($minutes);
        $this->update();
    }

    public function sendEmailVerificationNotification()
    {
        return false;
    }

    public function logDetails()
    {
        return $this->first_name . " " . $this->last_name . " " . $this->account_number;
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->user_type === self::ADMIN;
    }

    public function getIsMemberAttribute(): bool
    {
        return $this->user_type === self::MEMBER;
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->user_status === UserInterface::ACCOUNT_SUSPENDED;
    }

    public function getIsPersonalAttribute(): bool
    {
        return $this->account_type === Account::PERSONAL;
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getLastTransaction(): Transaction
    {
        return Transaction::where('user_id', $this->user_id)->orderBy('created_at', 'DESC')->first();
    }

    public function session(): HasOne
    {
        return $this->hasOne(Session::class, 'user_id');
    }

    public function restriction(): BelongsTo
    {
        return $this->belongsTo(RestrictionTemplate::class, 'restriction_id');
    }

    public function isKycStatusIs(string $status): bool
    {
        return $this->kyc_status === $status;
    }

    public function hasBrgyAccess() // for tagger
    {
        $restrictions = json_decode($this->restriction_properties, true);
        return isset($restrictions['brgy_access']) && !empty($restrictions['brgy_access']);
    }

    public function hasActivityLogAccess() // for tagger
    {
        $restrictions = json_decode($this->restriction_properties, true);
        return isset($restrictions['has_activity_logs_access']) && (bool)$restrictions['has_activity_logs_access'];
    }
}
