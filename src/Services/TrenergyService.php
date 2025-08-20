<?php
declare(strict_types=1);

namespace Apd\Trenergy\Services;

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
use Apd\Trenergy\Enums\AML\Blockchain;
use Apd\Trenergy\Enums\Consumers\PaymentPeriod;
use Apd\Trenergy\Enums\Stake\StakeProfitabilityPeriodEnum;
use Apd\Trenergy\Exceptions\TrenergyWrongPaymentPeriod;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Apd\Trenergy\Exceptions\TrenergyServerErrorException;

class TrenergyService extends BaseService
{
    protected Client $client;

    public function __construct(?Client $client = null)
    {
        parent::__construct(); // Явный вызов родительского конструктора

        $this->client = $client ?? new Client([
            'base_uri' => config('trenergy.base-url'),
            'headers' => array_merge(
                ['Authorization' => 'Bearer ' . config('trenergy.api-key')],
                config('trenergy.headers')
            )
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getAccount(): AccountDTO|array
    {
        $body = $this
            ->setEndPoint('account')
            ->setMethod('GET')
            ->sendGetContent();

        return $this->result(
            AccountDTO::class,
            $body
        );

    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getTopUp(): AccountTopUpDTO|array
    {
        $body = $this
            ->setEndPoint('account/top-up')
            ->setMethod('GET')
            ->sendGetContent();

        return $this->result(
            AccountTopUpDTO::class,
            $body
        );
    }

    public function subscribe(bool $isBalanceUsed, ?bool $isCredit = null)
    {
        $params = [
            'is_balance_used' => (int) $isBalanceUsed
        ];

        if (!is_null($isCredit)) {
            $params['is_credit'] = (int) $isCredit;
        }

        $body = $this
            ->setMethod('POST')
            ->setEndPoint('account/subscribe')
            ->setParams('json', $params)
            ->sendGetContent();

        return $this->result(SubscribeDTO::class, $body);
    }

    /**
     * @return \Illuminate\Support\Collection<ConsumerDTO>
     *
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getConsumers(): Collection|array
    {
        $body = $this
            ->setEndPoint('consumers')
            ->setMethod('GET')
            ->sendGetContent();

        return $this->result(ConsumerDTO::class, $body, true);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     * @throws \Throwable
     */
    public function downLoadConsumerList(
        ?string $name = null,
        ?bool $isActive = null,
        ?bool $autoRenewal = null,
        ?array $paymentPeriods = [],
        ?array $wallet = null,
        ?string $format = null
    ): string {

        $params = [];

        if ($name) {
            $params['name'] = $name;
        }

        if ($isActive) {
            $params['is_active'] = (int) $isActive;
        }

        if ($autoRenewal) {
            $params['auto_renewal'] = (int) $autoRenewal;
        }

        if (!empty($paymentPeriods)) {
            foreach ($paymentPeriods as $paymentPeriod) {
                if (!PaymentPeriod::tryFrom($paymentPeriod)) {
                    throw new TrenergyWrongPaymentPeriod();
                }
            }

            $params['payemnt_periods'] = $paymentPeriods;
        }

        if (!empty($wallet)) {
            $params['wallet'] = $wallet;
        }

        if ($format) {
            throw_if($format, ['xlsx', 'csv'], new TrenergyServerErrorException('Available formats is xlsx or csv'));
            $params['format'] = $format;
        }

        if (empty($params)) {
            $query =  $this
                ->setEndPoint('consumers/download')
                ->setMethod('GET')
                ->setParams('json', $params);
        } else {
            $query =  $this
                ->setEndPoint('consumers/download')
                ->setMethod('GET');
        }

        return $query->sendGetContent();
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function createConsumer(
        string $paymentPeriod,
        string $address,
        float $resourceAmount,
        string $name,
        int $autoRenewal = 0
    ) {
        $body = $this
            ->setEndPoint('consumers')
            ->setMethod('POST')
            ->setHeaders(['Service-lang' => 'en'])
            ->setParams('json', [
                'payment_period' => $paymentPeriod,
                'address' => $address,
                'auto_renewal' => $autoRenewal,
                'resource_amount' => $resourceAmount,
                'name' => $name
            ])
            ->sendGetContent();

        return $this->result(OrderCreatedDTO::class, $body);
    }

    public function getConsumer(int $consumerId)
    {
        $body = $this
            ->setEndPoint('consumers/' . $consumerId)
            ->setMethod('GET')
            ->sendGetContent();

        return $this->result(ConsumerDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function activateConsumer(int $consumerId)
    {
        $body = $this
            ->setEndPoint('consumers/' . $consumerId . '/activate')
            ->setMethod('POST')
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    public function createAndActivate(
        int $paymentPeriod,
        string $address,
        int $autoRenewal = 0,
        float $resourceAmount,
        ?string $name = null,
        ?string $webHookUrl = null
    ) {
        
        if(PaymentPeriod::tryFrom($paymentPeriod) === null) {
            throw new TrenergyWrongPaymentPeriod();
        }

        $params = [
            'payment_period' => $paymentPeriod,
            'address' => $address,
            'auto_renewal' => (int) (bool) $autoRenewal,
            'resource_amount' => $resourceAmount
        ];

        if ($name) {
            $params['name'] = $name;
        }

        if ($webHookUrl) {
            $params['webhook_url'] = $webHookUrl;
        }

        $body = $this
            ->setEndPoint('consumers/bootstrap-order')
            ->setMethod('GET')
            ->setParams('json', $params)
            ->sendGetContent();

        return $this->result(OrderCreatedDTO::class, $body);
    }

    public function deActivateConsumer(int $consumerId)
    {
        $body = $this
            ->setEndPoint('consumers/' . $consumerId . '/deactivate')
            ->setMethod('POST')
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws TrenergyWrongPaymentPeriod
     * @throws \JsonException
     */
    public function updateConsumer(
        int $consumerId,
        float $resourceAmount,
        int $paymentPeriod,
        bool $autoRenewal,
        ?string $name = null
    ) {

        if (!PaymentPeriod::tryFrom($paymentPeriod)) {
            throw new TrenergyWrongPaymentPeriod();
        }

        if ($name) {
            $param['name'] = $name;
        }

        $param['payment_period'] = (string) $paymentPeriod;
        $param['auto_renewal'] = (string) (int) $autoRenewal;
        $param['resource_amount'] = (string) $resourceAmount;

        $formData = http_build_query($param, '', '&');

        $body = $this
            ->setMethod('PATCH')
            ->setEndPoint('consumers/' . $consumerId)
            ->setHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->setParams('body', $formData)
            ->sendGetContent();

        return $this->result(ConsumerDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function destroyConsumer(int $consumerId)
    {
        $body = $this
            ->setMethod('DELETE')
            ->setEndPoint('consumers/' . $consumerId)
        ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function toggleAutoRenewal(bool $autoRenewal, array $consumersIds)
    {
        $body = $this
            ->setMethod('POST')
            ->setEndPoint('consumers/auto-renewal')
            ->setParams('json', ['auto_renewal' => (int) $autoRenewal, 'consumers' => $consumersIds])
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function consumerBlockchainEnergy(int $consumerId)
    {
        $body = $this
            ->setMethod('GET')
            ->setEndPoint('consumers/' . $consumerId . '/blockchain-energy')
            ->sendGetContent();

        return $this->result(BlockchainEnergyDTO::class, $body);
    }

    public function consumerMassTrx(float $amount, array $consumersIds)
    {
        $body = $this
            ->setMethod('POST')
            ->setEndPoint('consumers/mass/trx')
            ->setParams('json', ['amount' => $amount, 'consumers' => $consumersIds])
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    public function activateTronAddress(string $address)
    {
        $body = $this
            ->setMethod('POST')
            ->setEndPoint('extra/activate-address')
            ->setParams('json', ['address' => $address])
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function consumerResetValidity(int $consumerId)
    {
        $body = $this
            ->setMethod('PATCH')
            ->setEndPoint('consumers/' . $consumerId . '/reset-validity')
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function consumerSummary()
    {
        $body = $this->setMethod('GET')->setEndPoint('consumers/summary')->sendGetContent();

        return $this->result(ConsumerSummaryDTO::class, $body);
    }

    public function consumerAddressReport(string $address, ?string $fromDate = null, ?string $toDate = null)
    {
        if ($fromDate) {
            $param['from_date'] = $this->dateArgumentValidation($fromDate);
        }

        if ($toDate) {
            $param['to_date'] = $this->dateArgumentValidation($toDate);
        }

        $param['address'] = $address;

        $formData = http_build_query($param , '', '&');

        $body = $this
            ->setEndPoint('consumers/address-report?' . $formData)
            ->setParams('body', $formData)
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function consumersConsumptionStats(string $fromDate, string $toDate, ?int $perPage = null)
    {
        $param['from_date'] = $this->dateArgumentValidation($fromDate);
        $param['to_date'] = $this->dateArgumentValidation($toDate);

        if ($perPage) {
            $param['per_page'] = $perPage;
        }

        $formData = http_build_query($param , '', '&');

        $body = $this
            ->setEndPoint('consumers/consumption-stats?' . $formData)
            ->sendGetContent();

        return $this->result(ConsumptionStatTotalDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function consumerMassPaymentPeriod(array $consumersIds, int $paymentPeriod, bool $autoRenewal)
    {
        if (is_null(PaymentPeriod::tryFrom($paymentPeriod))) {
            throw new InvalidArgumentException("Payment period must be one of 15, 60, 480, 1440.");
        }

        $body = $this
            ->setMethod('POST')
            ->setEndPoint('consumers/mass/payment-period')
            ->setParams('json', [
                'consumer_ids' => $consumersIds,
                'payment_period' => $paymentPeriod,
                'auto_renewal' => (int) $autoRenewal
            ])
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function amlList(?string $fromDate = null, ?string $toDate = null, ?int $perPage = null)
    {
        $param = [];

        if ($fromDate) {
            $param['from_date'] = $this->dateArgumentValidation($fromDate);
        }

        if ($toDate) {
            $param['to_date'] = $this->dateArgumentValidation($toDate);
        }

        if ($perPage) {
            $param['per_page'] = $perPage;
        }

        $formData = http_build_query($param , '', '&');

        $body = $this->setEndPoint('aml?' . $formData)->sendGetContent();

        return $this->result(AmlListDTO::class, $body, true);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function amlCheck(string $blockchain, ?string $address = null, ?string $txid = null)
    {
        if (!Blockchain::tryFrom($blockchain)) {
            throw new InvalidArgumentException("Blockchain must be one of 'tron' or 'btc'.");
        }

        if (!$address && !$txid) {
            throw new InvalidArgumentException("one of the arguments adress or txid must be filled");
        }

        if ($blockchain === Blockchain::btc->value && !$address) {
            throw new InvalidArgumentException("If blockchain is 'btc' then address must be filled.");
        }

        $params['blockchain'] = $blockchain;
        if ($address) {
            $params['address'] = $address;
        }

        if ($txid) {
            $params['txid'] = $txid;
        }

        $body = $this
            ->setMethod('POST')
            ->setEndPoint('aml/check')
            ->setParams('json', $params)
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getWithdrawals(?int $perPage = 5)
    {
        $body = $this->setEndPoint('withdrawals?per_page=' . $perPage ?? '5')->sendGetContent();

        return $this->result(GetWithdrawalsDTO::class, $body, true);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function withdrawals(float $trxAmount, string $address, string $oneTimePassword)
    {
        $body = $this
            ->setMethod('POST')
            ->setEndPoint('withdrawals')
            ->setParams('json', [
                'trx_amount' => $trxAmount,
                'address' => $address,
                'one_time_password' => $oneTimePassword
            ])
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    public function stakes(int $perPage = 5)
    {
        $body = $this->setEndPoint('stakes?per_page=' . $perPage)->sendGetContent();

        return $this->result(GetStakeDTO::class, $body, true);
    }

    public function stake(float $trxAmount)
    {
        $body = $this
            ->setMethod('POST')
            ->setEndPoint('stakes')
            ->setParams('json', ['trx_amount' => $trxAmount])
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    public function unstake(float $trxAmount, $oneTimePassword)
    {
        $body = $this
            ->setMethod('POST')
            ->setEndPoint('stakes/unstake')
            ->setParams('json', ['trx_amount' => $trxAmount, 'one_time_password' => $oneTimePassword])
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    public function stakeSync()
    {
        $body = $this
            ->setMethod('POST')
            ->setEndPoint('stakes/sync')
            ->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    public function stakeProfitability(?int $period = null)
    {
        if ($period) {
            $periodValue = StakeProfitabilityPeriodEnum::tryFrom($period);
            if (!$periodValue) {
                throw new InvalidArgumentException("Period must be one of 7, 30, 365");
            }
            $endPoint = 'stakes/profitability?period=' . $periodValue->value;
        } else {
            $endPoint = 'stakes/profitability';
        }


        $body = $this->setEndPoint($endPoint)->sendGetContent();

        return $this->result(StakeProfitabilityDTO::class, $body, true);
    }

    public function partners(int $leaderId = null)
    {
        $endpoint = 'structure/partners';

        if ($leaderId) {
            $endpoint .= '?leader=' . $leaderId;
        }

        $body = $this->setEndPoint($endpoint)->sendGetContent();

        return $this->result(StructureDTO::class, $body, true);
    }

    public function getWallets()
    {
        $body = $this->setEndPoint('wallets')->sendGetContent();

        return $this->result(WalletDTO::class, $body, true);
    }

    public function addWallet(string $address)
    {
        $body = $this
            ->setMethod('POST')
            ->setParams('json', ['address' => $address])
            ->setEndPoint('wallets')->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    public function dropWallet(int $walletId, string $oneTimePassword)
    {
        $body = $this
            ->setMethod('DELETE')
            ->setEndPoint('wallets/' . $walletId . '?one_time_password=' . $oneTimePassword)->sendGetContent();

        return $this->result(ArrayDTO::class, $body);
    }

    protected function result(string $dtoClass, string|array $body, bool $isCollection = false)
    {

        if (is_array($body)) {
            return $body;
        }

        $body = json_decode($body, true, 512, JSON_THROW_ON_ERROR)['data'];

        if ($isCollection && is_array($body)) {
            $collection = new Collection();
            foreach ($body as $item) {
                $collection->add(new $dtoClass($item));
            }

            return $collection;
        }

        return new $dtoClass($body);
    }

    protected function dateArgumentValidation(string $date): string
    {
        if (!DateTime::createFromFormat('Y-m-d', $date)) {
            throw new InvalidArgumentException("Incorrect date format. Y-m-d expected");
        }

        return $date;
    }
}
