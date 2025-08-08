<?php
declare(strict_types=1);

namespace Apd\Trenergy\Facades;

use Apd\Trenergy\DTO\Account\AccountDTO;
use Apd\Trenergy\DTO\Account\AccountTopUpDTO;
use Apd\Trenergy\DTO\Account\SubscribeDTO;
use Apd\Trenergy\DTO\Aml\AmlListDTO;
use Apd\Trenergy\DTO\ArrayDTO;
use Apd\Trenergy\DTO\Consumers\BlockchainEnergyDTO;
use Apd\Trenergy\DTO\Consumers\ConsumerDTO;
use Apd\Trenergy\DTO\Consumers\ConsumerSummaryDTO;
use Apd\Trenergy\DTO\Consumers\ConsumptionStatTotalDTO;
use Apd\Trenergy\DTO\Consumers\OrderCreatedDTO;
use Apd\Trenergy\DTO\Partners\PartnerDTO;
use Apd\Trenergy\DTO\Partners\StructureDTO;
use Apd\Trenergy\DTO\Stakes\GetStakeDTO;
use Apd\Trenergy\DTO\Stakes\StakeProfitabilityDTO;
use Apd\Trenergy\DTO\Wallets\WalletDTO;
use Apd\Trenergy\DTO\Withdrawals\GetWithdrawalsDTO;
use Apd\Trenergy\Services\TrenergyService;
use Carbon\Traits\Date;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static AccountDTO|array getAccount()
 * @method static AccountTopUpDTO|array getTopUp()
 * @method static Collection<ConsumerDTO>|array getConsumers() Возвращает коллекцию ConsumerDTO
 * @method static string downLoadConsumerList()
 * @method static OrderCreatedDTO|array createConsumer(string $paymentPeriod, string $address, float $resourceAmount, string $name, int $autoRenewal = 0)
 * @method static ConsumerDTO|array getConsumer(int $consumerId)
 * @method static ArrayDTO|array activateConsumer(int $consumerId)
 * @method static ArrayDTO|array deActivateConsumer(int $consumerId)
 * @method static ConsumerDTO|array updateConsumer(int $consumerId, float $resourceAmount, int $paymentPeriod, bool $autoRenewal, ?string $name = null)
 * @method static ArrayDTO|array destroyConsumer(int $consumerId)
 * @method static ArrayDTO|array toggleAutoRenewal(bool $autoRenewal, array $consumersIds)
 * @method static BlockchainEnergyDTO|array consumerBlockchainEnergy(int $consumerId)
 * @method static ArrayDTO|array consumerMassTrx(float $amount, array $consumersIds)
 * @method static ArrayDTO|array activateTronAddress(string $address)
 * @method static ArrayDTO|array consumerResetValidity(int $consumerId)
 * @method static ConsumerSummaryDTO|array consumerSummary()
 * @method static ArrayDTO|array consumerAddressReport(string $address, ?string $fromDate = null, ?string $toDate = null)
 * @method static ConsumptionStatTotalDTO|array consumersConsumptionStats(string $fromDate, string $toDate, ?int $perPage = null)
 * @method static ArrayDTO|array consumerMassPaymentPeriod(array $consumersIds, int $paymentPeriod, bool $autoRenewal)
 * @method static AmlListDTO|array amlList(?string $fromDate = null, ?string $toDate = null, ?int $perPage = null)
 * @method static ArrayDTO|array amlCheck(string $blockchain, ?string $address = null, ?string $txid = null)
 * @method static ArrayDTO|array buyEnergy(string $paymentPeriod, string $address, float $resourceAmount, string $name, int $autoRenewal = 0)
 * @method static GetWithdrawalsDTO|array getWithdrawals(int $perPage = 5)
 * @method static ArrayDTO|array withdrawals(float $trxAmount, string $address, string $oneTimePassword)
 * @method static SubscribeDTO|array subscribe(bool $isBalanceUsed, bool $isCredit = false)
 * @method static GetStakeDTO|array stakes(int $perPage = 5)
 * @method static ArrayDTO|array stake(float $trxAmount)
 * @method static ArrayDTO|array unstake(float $trxAmount, int $oneTimePassword)
 * @method static ArrayDTO|array stakeSync()
 * @method static StakeProfitabilityDTO|array stakeProfitability(?int $period = null)
 * @method static StructureDTO|array partners(int $leaderId = null)
 * @method static Collection<WalletDTO>|array getWallets() Возвращает коллекцию WalletDTO
 * @method static ArrayDTO|array addWallet(string $address)
 * @method static ArrayDTO|array dropWallet(int $walletId, string $oneTimePassword)
 *
 * @see TrenergyService
 */

class Trenergy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'trenergy.service';
    }
}
