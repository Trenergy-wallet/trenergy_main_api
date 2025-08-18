<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Account;

use Apd\Trenergy\DTO\ArrayDTO;
use Apd\Trenergy\DTO\BaseDTO;

/**
 * @property string $name Full name of the user
 * @property string $email User's email address
 * @property bool $has_password Whether the user has a password set
 * @property string $lang Language preference (e.g. 'en', 'ru')
 * @property string $the_code Unique user identifier code
 * @property string|null $invitation_code Invitation code used for registration
 * @property int $credit_limit Maximum credit limit allowed
 * @property int $type User type identifier
 * @property string|null $leader_name Name of the leader/referrer
 * @property int $leader_level Level in referral hierarchy
 * @property bool $ref_enabled Whether referrals are enabled
 * @property int $consumer_coefficient Base consumer coefficient
 * @property int $consumer_coefficient_extra Additional consumer coefficient
 * @property bool $is_banned Whether the user is banned
 * @property bool $balance_restricted Whether balance is restricted
 * @property string $photo URL/path to profile photo
 * @property float $stakes_sum Total sum of all stakes
 * @property float $stakes_profit Total profit from stakes
 * @property float $available_to_unstake_sum Amount available for unstaking
 * @property float $active_stakers_count Count of active stakers
 * @property bool $TwoFa Whether 2FA is enabled
 * @property int $onboarding Onboarding completion status
 * @property string $created_at Account creation date (ISO 8601)
 * @property string $updated_at Last update date (ISO 8601)
 * @property string|null $deletion_at Scheduled deletion date
 * @property string|null $reinvestment Reinvestment preference
 * @property array $notification_settings User's notification settings
 * @property string|null $subscription Active subscription plan
 * @property float $balance Current account balance
 * @property float $energy_balance Current energy balance
 **/
class AccountDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public bool $has_password;
    public string $lang;
    public string $the_code;
    public ?string $invitation_code;
    public int $credit_limit;
    public int $type;
    public ?string $leader_name;
    public int $leader_level;
    public bool $ref_enabled;
    public int $consumer_coefficient;
    public int $consumer_coefficient_extra;
    public bool $is_banned;
    public bool $balance_restricted;
    public string $photo;
    public float $stakes_sum;
    public float $stakes_profit;
    public float $available_to_unstake_sum;
    public float $active_stakers_count;
    public bool $TwoFa;
    public int $onboarding;
    public string $created_at;
    public string $updated_at;
    public ?string $deletion_at;
    public ?ReinvestmentDTO $reinvestment;
    public array $notification_settings;
    public ?AccountSubscriptionDTO $subscription;
    public float $balance = 0.0;
    public float $energy_balance;

    protected array $nestedDtos = [
        'reinvestment' => ReinvestmentDTO::class,
        'subscription' => AccountSubscriptionDTO::class,
        'notification_settings' => AccountNotificationSettingsDTO::class,
    ];
}
