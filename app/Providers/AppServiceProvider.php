<?php

namespace App\Providers;

use App\Repositories\Account\AccountInterface;
use App\Repositories\Account\AccountRepository;
use App\Repositories\AccountFee\AccountFeeInterface;
use App\Repositories\AccountFee\AccountFeeRepository;

use App\Repositories\ActivityLog\ActivityLogInterface;
use App\Repositories\ActivityLog\ActivityLogRepository;

use App\Repositories\CannedMessage\CannedMessageInterface;
use App\Repositories\CannedMessage\CannedMessageRepository;

use App\Repositories\Country\CountryInterface;
use App\Repositories\Country\CountryRepository;

use App\Repositories\Currency\CurrencyInterface;
use App\Repositories\Currency\CurrencyRepository;

use App\Repositories\Maintenance\MaintenanceInterface;
use App\Repositories\Maintenance\MaintenanceRepository;

use App\Repositories\Note\NoteInterface;
use App\Repositories\Note\NoteRepository;

use App\Repositories\PasswordResets\PasswordResetsInterface;
use App\Repositories\PasswordResets\PasswordResetsRepository;

use App\Repositories\Permission\PermissionInterface;
use App\Repositories\Permission\PermissionRepository;

use App\Repositories\RestrictionTemplate\RestrictionTemplateInterface;
use App\Repositories\RestrictionTemplate\RestrictionTemplateRepository;

use App\Repositories\Role\RoleInterface;
use App\Repositories\Role\RoleRepository;

use App\Repositories\RoleHasPermission\RoleHasPermissionInterface;
use App\Repositories\RoleHasPermission\RoleHasPermissionRepository;

use App\Repositories\Security\SecurityInterface;
use App\Repositories\Security\SecurityRepository;

use App\Repositories\Setting\SettingInterface;
use App\Repositories\Setting\SettingRepository;

use App\Repositories\Ticket\TicketInterface;
use App\Repositories\Ticket\TicketRepository;

use App\Repositories\TicketConversation\TicketConversationInterface;
use App\Repositories\TicketConversation\TicketConversationRepository;

use App\Repositories\Transaction\TransactionInterface;
use App\Repositories\Transaction\TransactionRepository;

use App\Repositories\User\UserInterface;
use App\Repositories\User\UserRepository;

use App\Repositories\UserDocument\UserDocumentInterface;
use App\Repositories\UserDocument\UserDocumentRepository;

use App\Repositories\WelcomeMessage\WelcomeMessageInterface;
use App\Repositories\WelcomeMessage\WelcomeMessageRepository;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AccountInterface::class, AccountRepository::class);
        $this->app->singleton(ActivityLogInterface::class, ActivityLogRepository::class);
        $this->app->singleton(SettingInterface::class, SettingRepository::class);
        $this->app->singleton(TransactionInterface::class, TransactionRepository::class);
        $this->app->singleton(PermissionInterface::class, PermissionRepository::class);
        $this->app->singleton(PasswordResetsInterface::class, PasswordResetsRepository::class);
        $this->app->singleton(RoleInterface::class, RoleRepository::class);
        $this->app->singleton(RoleHasPermissionInterface::class, RoleHasPermissionRepository::class);
        $this->app->singleton(UserDocumentInterface::class, UserDocumentRepository::class);
        $this->app->singleton(UserInterface::class, UserRepository::class);
        $this->app->singleton(AccountFeeInterface::class, AccountFeeRepository::class);
        $this->app->singleton(CurrencyInterface::class, CurrencyRepository::class);
        $this->app->singleton(SecurityInterface::class, SecurityRepository::class);
        $this->app->singleton(TicketInterface::class, TicketRepository::class);
        $this->app->singleton(TicketConversationInterface::class, TicketConversationRepository::class);
        $this->app->singleton(CannedMessageInterface::class, CannedMessageRepository::class);
        $this->app->singleton(WelcomeMessageInterface::class, WelcomeMessageRepository::class);
        $this->app->singleton(CountryInterface::class, CountryRepository::class);
        $this->app->singleton(RestrictionTemplateInterface::class, RestrictionTemplateRepository::class);
        $this->app->singleton(NoteInterface::class, NoteRepository::class);
        $this->app->singleton(MaintenanceInterface::class, MaintenanceRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
