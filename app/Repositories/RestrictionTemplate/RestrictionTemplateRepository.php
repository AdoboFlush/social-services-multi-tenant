<?php

namespace App\Repositories\RestrictionTemplate;

use App\Deposit;
use App\DepositCard;
use App\Exceptions\RestrictionException;
use App\KycStatus;
use App\Repositories\ExchangeRate\ExchangeRateRepository;
use App\RestrictionTemplate;
use App\Transaction;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class RestrictionTemplateRepository implements RestrictionTemplateInterface
{
    private $user;

    private const GREATER_THAN = ">";
    private const LESS_THAN = "<";

    public function __construct(
        ExchangeRateRepository $exchangeRateRepository,
        RestrictionTemplate $model
    ) {
        $this->user = Auth::user();
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->model = $model;
    }

    private function hasExceededAmountLimit(
        float $amount,
        string $currency,
        float $limit,
        string $condition
    ): bool {
        $hasExceeded = false;

        if ($limit > 0) {

            $finalAmount = $amount;

            if ($this->user->restriction->base_currency != $currency) {
                $exchangeRate = $this->exchangeRateRepository->getExchangeRate(
                    $currency,
                    $this->user->restriction->base_currency,
                    1
                );
                $finalAmount = $amount * $exchangeRate->rate;
            }

            if ($condition === self::GREATER_THAN) {
                $hasExceeded = $finalAmount > $limit;
            } else if ($condition === self::LESS_THAN) {
                $hasExceeded = $finalAmount < $limit;
            } else {
                throw new Exception('Unkown condition when checking transaction amount');
            }
        }

        return $hasExceeded;
    }

    private function hasExceededLimit(Carbon $startDate, Carbon $endDate, int $limit): bool
    {
        /**
         * set this to -1 so that if the limit is 0, it will return true (0 = no limit)
         */
        $total = -1;
        $restriction_transaction_type = $this->user->restriction->transaction_type;
        $restriction_transaction_method = $this->user->restriction->transaction_method;

        if ($limit > 0) {
            $transactions = Transaction::getTransactionsBetweenCreatedDates(
                $this->user->id,
                $this->user->restriction->transaction_type,
                $startDate,
                $endDate
            );

            if (
                $restriction_transaction_type === Transaction::DEPOSIT
                && $restriction_transaction_method === Deposit::WIRE_TRANSFER
            ) {
                $total = $transactions->has('depositCard')->get()->count() + 1;
            }
        }

        return $total > $limit;
    }

    private function hasExceededAttemptLimit(Carbon $startDate, Carbon $endDate, int $limit): bool
    {

        $total = -1;
        if ($limit > 0) {
            $activities = Activity::where('log_name', DepositCard::ACT_LOG_CARD_DEPOSIT_NAME)
                            ->where('description', DepositCard::ACT_LOG_CARD_DEPOSIT_DESCRIPTION)
                            ->where('causer_id', $this->user->id)
                            ->whereBetween('created_at', [
                                $startDate,
                                $endDate
                            ]); 
            $total = $activities->get()->count() + 1;
        }

        return $total > $limit;
    }

    public function applyMinimumTransactionLimit(float $amount, string $currency): self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededAmountLimit(
                $amount,
                $currency,
                $this->user->restriction->minimum_transaction_amount,
                self::LESS_THAN
            )) {
                throw new RestrictionException('Invalid amount. Please enter an amount from USD {min} to USD {max}, or its equivalent amount in other currencies.', [
                    'min' => $this->user->restriction->minimum_transaction_amount,
                    'max' => $this->user->restriction->maximum_transaction_amount
                ]);
            }
        }

        return $this;
    }

    public function applyMaximumTransactionLimit(float $amount, string $currency): self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededAmountLimit(
                $amount,
                $currency,
                $this->user->restriction->maximum_transaction_amount,
                self::GREATER_THAN
            )) {
                throw new RestrictionException('Invalid amount. Please enter an amount from USD {min} to USD {max}, or its equivalent amount in other currencies.', [
                    'min' => $this->user->restriction->minimum_transaction_amount,
                    'max' => $this->user->restriction->maximum_transaction_amount
                ]);
            }
        }

        return $this;
    }

    public function applyHourlyLimit(): self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededLimit(
                Carbon::now()->startOfHour(),
                Carbon::now()->endOfHour(),
                $this->user->restriction->transactions_per_hour
            )) {
                /**
                 * @todo add more detail when checks failed (e,g user's name, total transactions. etc..)
                 */
                throw new RestrictionException('You have exceeded your hourly transaction limit. Please try again later.');
            }
        }

        return $this;
    }

    public function applyDailyLimit(): self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededLimit(
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
                $this->user->restriction->transactions_per_day
            )) {
                /**
                 * @todo add more detail when checks failed (e,g user's name, total transactions. etc..)
                 */
                throw new RestrictionException('You have exceeded your daily transaction limit. Please try again after 24 hours.');
            }
        }

        return $this;
    }

    public function applyWeeklyLimit(): self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededLimit(
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
                $this->user->restriction->transactions_per_week
            )) {
                /**
                 * @todo add more detail when checks failed (e,g user's name, total transactions. etc..)
                 */
                throw new RestrictionException('You have exceeded your weekly transaction limit.');
            }
        }

        return $this;
    }

    public function applyMonthlyLimit(): self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededLimit(
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                $this->user->restriction->transactions_per_month
            )) {
                /**
                 * @todo add more detail when checks failed (e,g user's name, total transactions. etc..)
                 */
                throw new RestrictionException('You have exceeded your monthly transaction limit.');
            }
        }

        return $this;
    }

    public function applyYearlyLimit(): self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededLimit(
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
                $this->user->restriction->transactions_per_year
            )) {
                /**
                 * @todo add more detail when checks failed (e,g user's name, total transactions. etc..)
                 */
                throw new RestrictionException('You have exceeded your yearly transaction limit.');
            }
        }

        return $this;
    }

    public function rejectDormant(): self
    {
        if ($this->user->restriction) {
            if ($this->user->is_dormant && !$this->user->restriction->allow_dormant) {
                throw new RestrictionException("dormant account is not allowed to this transaction");
            };
        }

        return $this;
    }

    public function rejectUnverifiedKYC(): self
    {
        if ($this->user->restriction) {
            if (
                strtolower($this->user->kyc_status) != strtolower(KycStatus::VERIFIED)
                && !$this->user->restriction->allow_unverified_kyc
            ) {
                throw new RestrictionException("your account is not fully verified to do this transaction");
            };
        }

        return $this;
    }

    public function applyMonthlyCardDepositLimit(float $amount, string $currency): self
    {
        if ($this->user->restriction) {
            $card_deposits = Transaction::getTransactionsBetweenCreatedDates(
                $this->user->id,
                Transaction::DEPOSIT,
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            )
                ->has('depositCard')
                ->get();

            $total_card_deposit_amount = $card_deposits->sum(function ($card_deposit) {
                $amount = $card_deposit->amount;
                if ($this->user->restriction->base_currency != $card_deposit->currency) {
                    $exchangeRate = $this->exchangeRateRepository->getExchangeRate(
                        $card_deposit->currency,
                        $this->user->restriction->base_currency,
                        1
                    );
                    $amount *= $exchangeRate->rate;
                }
                return $amount;
            });

            if ($this->user->restriction->base_currency != $currency) {
                $exchangeRate = $this->exchangeRateRepository->getExchangeRate(
                    $currency,
                    $this->user->restriction->base_currency,
                    1
                );
                $amount *= $exchangeRate->rate;
            }

            $total_amount_for_this_month = $total_card_deposit_amount + $amount;
            $max_card_deposit_per_month = $this->user->restriction->max_deposit_amount_per_month;

            if ($total_amount_for_this_month > $max_card_deposit_per_month) {
                throw new RestrictionException("You have exceeded your monthly transaction amount limit.");
            }
        }

        return $this;
    }

    public function applyAttemptHourlyLimit() : self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededAttemptLimit(
                Carbon::now()->startOfHour(),
                Carbon::now()->endOfHour(),
                $this->user->restriction->max_deposit_card_attempts_per_hour
            )) {
                throw new RestrictionException('You have exceeded your hourly attempts limit.');
            }
        }
        return $this;
    }

    public function applyAttemptDailyLimit() : self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededAttemptLimit(
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
                $this->user->restriction->max_deposit_card_attempts_per_day
            )) {
                throw new RestrictionException('You have exceeded your daily attempts limit.');
            }
        }
        return $this;
    }

    public function applyAttemptWeeklyLimit() : self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededAttemptLimit(
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
                $this->user->restriction->max_deposit_card_attempts_per_week
            )) {
                throw new RestrictionException('You have exceeded your weekly attempts limit.');
            }
        }
        return $this;
    }

    public function applyAttemptMonthlyLimit() : self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededAttemptLimit(
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                $this->user->restriction->max_deposit_card_attempts_per_month
            )) {
                throw new RestrictionException('You have exceeded your monthly attempts limit.');
            }
        }
        return $this;
    }

    public function applyAttemptYearlyLimit() : self
    {
        if ($this->user->restriction) {
            if ($this->hasExceededAttemptLimit(
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
                $this->user->restriction->max_deposit_card_attempts_per_year
            )) {
                throw new RestrictionException('You have exceeded your yearly attempts limit.');
            }
        }
        return $this;
    }

}
