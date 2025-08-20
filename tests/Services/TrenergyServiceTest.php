<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\Services;

use Apd\Trenergy\DTO\Account\AccountDTO;
use Apd\Trenergy\DTO\Account\AccountTopUpDTO;
use Apd\Trenergy\DTO\Account\SubscribeDTO;
use Apd\Trenergy\DTO\Aml\AmlListContextDTO;
use Apd\Trenergy\DTO\Aml\AmlListDTO;
use Apd\Trenergy\DTO\ArrayDTO;
use Apd\Trenergy\DTO\Consumers\BlockchainEnergyDTO;
use Apd\Trenergy\DTO\Consumers\ConsumerDTO;
use Apd\Trenergy\DTO\Consumers\ConsumerSummaryDTO;
use Apd\Trenergy\DTO\Consumers\ConsumptionStatDTO;
use Apd\Trenergy\DTO\Consumers\ConsumptionStatTotalDTO;
use Apd\Trenergy\DTO\Consumers\OrderCreatedDTO;
use Apd\Trenergy\DTO\Consumers\OrderDTO;
use Apd\Trenergy\DTO\Partners\PartnerDTO;
use Apd\Trenergy\DTO\Partners\StructureDTO;
use Apd\Trenergy\DTO\Stakes\GetStakeDTO;
use Apd\Trenergy\DTO\Stakes\StakeProfitabilityDTO;
use Apd\Trenergy\DTO\Wallets\WalletDTO;
use Apd\Trenergy\DTO\Withdrawals\GetWithdrawalsDTO;
use Apd\Trenergy\Enums\Stake\StakeProfitabilityPeriodEnum;
use Apd\Trenergy\Exceptions\TrenergyWrongPaymentPeriod;
use Apd\Trenergy\Services\BaseService;
use Apd\Trenergy\Services\TrenergyService;
use Apd\Trenergy\Services\TrenergyConnector;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TrenergyServiceTest extends TestCase
{

    private $trenergyService;

    protected function setUp(): void
    {
        // 1. Создаем мок TrenergyConnector
        $connectorMock = $this->createMock(TrenergyConnector::class);
        $connectorMock->method('getBaseUrl')->willReturn('https://api.test.com/');
        $connectorMock->method('getApiKey')->willReturn('test-key');
        $connectorMock->method('getCommonHeaders')->willReturn([]);

        // 2. Создаем частичный мок TrenergyService
        $this->trenergyService = $this->getMockBuilder(TrenergyService::class)
            ->setConstructorArgs([new Client()])
            ->onlyMethods(['sendGetContent']) // Мокируем только sendGetContent
            ->getMock();

        // 4. Устанавливаем connector через reflection
        $reflection = new ReflectionClass($this->trenergyService);
        $connectorProperty = $reflection->getProperty('connector');
        $connectorProperty->setAccessible(true);
        $connectorProperty->setValue($this->trenergyService, $connectorMock);
    }

    public function test_get_account_with_mocked_send_get_content()
    {
        // 3. Настраиваем ожидаемый ответ от sendGetContent
        $mockResponse = json_encode([
            'data' => [
                'name' => 'Пользователь 493366',
                'email' => 'test@example.com',
                'has_password' => false,
                'lang' => 'ru',
                'the_code' => 'TEWMYatP',
                'invitation_code' => null,
                'credit_limit' => 0,
                'type' => 1,
                'leader_name' => null,
                'leader_level' => 0,
                'ref_enabled' => true,
                'consumer_coefficient' => 1,
                'consumer_coefficient_extra' => 1,
                'is_banned' => false,
                'balance_restricted' => false,
                'photo' => 'http://localhost:8081/img/default-avatar-1.png',
                'stakes_sum' => 0,
                'stakes_profit' => 0,
                'available_to_unstake_sum' => 0,
                'active_stakers_count' => 0,
                '2fa' => false,
                'onboarding' => 2,
                'created_at' => '20-06-2025 14:55:09',
                'updated_at' => '20-06-2025 14:55:09',
                'deletion_at' => null,
                'reinvestment' => null,
                'balance' => 1000,
            ],
            'status' => true
        ]);

        $this->trenergyService->method('sendGetContent')
            ->willReturn($mockResponse);

        // 5. Вызываем тестируемый метод
        $result = $this->trenergyService->getAccount();

        // 6. Проверяем результаты
        $this->assertInstanceOf(AccountDTO::class, $result);
        $this->assertEquals('Пользователь 493366', $result->name);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEquals(1000, $result->balance);
    }

    public function test_get_top_up()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                "address" => "TT7Eu4MJxdmiHwAvpH91n65Erg6Wk12345",
                "qr_code" => "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=TT7Eu4MJxdmiHwAvpH91n65Erg6Wk12345",
                "time_left" => 3599
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->getTopUp();

        // 6. Проверяем результаты
        $this->assertInstanceOf(AccountTopUpDTO::class, $result);
        $this->assertEquals('TT7Eu4MJxdmiHwAvpH91n65Erg6Wk12345', $result->address);
    }

    public function test_subscribe()
    {
        $mockResponse = [
            "status" => true,
            "data" => [
                "address" => "TT7Eu4MJxdmiHwAvpH91n65Erg6Wk12345",
                "qr_code" => "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=TT7Eu4MJxdmiHwAvpH91n65Erg6Wk12345",
                "time_left" => 1800,
                "amount" => 859.716669,
                "currency" => "TRX",
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->subscribe(true, true);
        // 6. Проверяем результаты
        $this->assertInstanceOf(SubscribeDTO::class, $result);
        $this->assertEquals('TT7Eu4MJxdmiHwAvpH91n65Erg6Wk12345', $result->address);

    }

    public function test_get_consumers()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                [
                    "id" => 1,
                    "name" => "First consumer",
                    "address" => "TWoHLT3JkUB1gwF745JuQmtcJ6rZ712345",
                    "resource_amount" => 245000,
                    "creation_type" => 2,
                    "payment_period" => 15,
                    "auto_renewal" => true,
                    "is_active" => true,
                    "order" => [
                        "status" => 3,
                        "completion_percentage" => 93,
                        "created_at" => "2023-10-11 16:06:23",
                        "updated_at" => "2023-10-22 09:18:02",
                        "valid_until" => "2023-11-24 12:55:00"
                    ],
                    "webhook_url" => "https://url.com",
                    "created_at" => "11-10-2023 16:06:23",
                    "updated_at" => "20-10-2023 12:47:49"
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->getConsumers();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(ConsumerDTO::class, $result->first());
        $this->assertInstanceOf(OrderDTO::class, $result->first()->order);
    }

    public function test_downLoadConsumerList_returns_binary_file_content()
    {
        // 1. Генерируем тестовые бинарные данные (7300 символов)
        $binaryData = str_repeat('0', 7300); // Или любой другой бинарный контент

        // 2. Мокаем sendGetContent для возврата бинарных данных
        $this->trenergyService->method('sendGetContent')
            ->willReturn($binaryData);

        // 3. Вызываем тестируемый метод
        $result = $this->trenergyService->downLoadConsumerList();

        // 4. Проверяем результаты
        $this->assertEquals(7300, strlen($result)); // Проверяем размер
        $this->assertEquals($binaryData, $result);  // Проверяем содержимое
    }

    public function test_create_consumer()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                "id" => 19487,
                "name" => "My consumer",
                "address" => "TY3dRk4eQ75dCrW7tUcCzggU9rnz4V1111",
                "resource_amount" => 2000,
                "desired_resource_amount" => 2000,
                "creation_type" => 2,
                "consumption_type" => 1,
                "recharge_type" => null,
                "payment_period" => 15,
                "auto_renewal" => false,
                "is_active" => false,
                "activation_queue" => false,
                "order" => null,
                "webhook_url" => null,
                "created_at" => "18-07-2025 07:14:37",
                "updated_at" => "18-07-2025 07:14:37"
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->createConsumer(
            '15',
            "TY3dRk4eQ75dCrW7tUcCzggU9rnz4V1111",
            2000,
            "My consumer"
        );

        $this->assertInstanceOf(OrderCreatedDTO::class, $result);
        $this->assertEquals(19487, $result->id);
    }

    public function test_get_consumer()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                "id" => 1,
                "name" => "First consumer",
                "address" => "TWoHLT3JkUB1gwF745JuQmtcJ6rZ712345",
                "resource_amount" => 245000,
                "creation_type" => 2,
                "payment_period" => 15,
                "auto_renewal" => true,
                "is_active" => true,
                "order" => [
                    "status" => 3,
                    "completion_percentage" => 93,
                    "created_at" => "2023-10-11 16:06:23",
                    "updated_at" => "2023-10-22 09:18:02",
                    "valid_until" => "2023-11-24 12:55:00"
                ],
                "webhook_url" => "https://url.com",
                "created_at" => "11-10-2023 16:06:23",
                "updated_at" => "20-10-2023 12:47:49"
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->getConsumer(1);

        $this->assertInstanceOf(ConsumerDTO::class, $result);
        $this->assertEquals("First consumer", $result->name);
    }

    public function test_activate_consumer()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->activateConsumer(1);

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_buy_energy_fail()
    {
        $mockResponseFail  = [
            "status" => false,
            "error" => 'some error',
            "errors" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn($mockResponseFail);

        $resultFail = $this->trenergyService->createAndActivate(
            15,
            "TY3dRk4eQ75dCrW7tUcCzggU9rnz4V1111",
            0,
            2000,
            "My consumer"
        );

        $this->assertEquals([
            "status" => false,
            "error" => 'some error',
            "errors" => []
        ], $resultFail);
    }

    public function test_buy_energy_success()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                "id" => 19487,
                "name" => "My consumer",
                "address" => "TY3dRk4eQ75dCrW7tUcCzggU9rnz4V1111",
                "resource_amount" => 2000,
                "desired_resource_amount" => 2000,
                "creation_type" => 2,
                "consumption_type" => 1,
                "recharge_type" => null,
                "payment_period" => 15,
                "auto_renewal" => false,
                "is_active" => false,
                "activation_queue" => false,
                "order" => null,
                "webhook_url" => null,
                "created_at" => "18-07-2025 07:14:37",
                "updated_at" => "18-07-2025 07:14:37"
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->buyEnergy(
            '15',
            "TY3dRk4eQ75dCrW7tUcCzggU9rnz4V1111",
            2000,
            "My consumer"
        );

        $this->assertEquals("TY3dRk4eQ75dCrW7tUcCzggU9rnz4V1111", $result->address);
    }

    public function test_deactivate_consumer()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->deActivateConsumer(1);

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_update_consumer_exception()
    {
        $this->expectException(TrenergyWrongPaymentPeriod::class);
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->updateConsumer(
            1,
            2000,
            123,
            true,
            'New Name'
        );
    }

    public function test_update_consumer()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                "id" => 1,
                "name" => "First consumer",
                "address" => "TWoHLT3JkUB1gwF745JuQmtcJ6rZ712345",
                "resource_amount" => 245000,
                "creation_type" => 2,
                "payment_period" => 15,
                "auto_renewal" => true,
                "is_active" => true,
                "order" => [
                    "status" => 3,
                    "completion_percentage" => 93,
                    "created_at" => "2023-10-11 16:06:23",
                    "updated_at" => "2023-10-22 09:18:02",
                    "valid_until" => "2023-11-24 12:55:00"
                ],
                "webhook_url" => "https://url.com",
                "created_at" => "11-10-2023 16:06:23",
                "updated_at" => "20-10-2023 12:47:49"
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->updateConsumer(
            1,
            2000,
            1440,
            true,
            'New Name'
        );

        $this->assertInstanceOf(ConsumerDTO::class, $result);
    }

    public function test_destroy_consumer()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->destroyConsumer(
            1,
        );

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_toggle_auto_renewal()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->toggleAutoRenewal(
            true,
            [1],
        );

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_consumer_blockchain_energy()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                "energy_free" => 62418,
                "energy_total" => 698052836
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->consumerBlockchainEnergy(1);

        $this->assertInstanceOf(BlockchainEnergyDTO::class, $result);
    }

    public function test_consumer_mass_trx()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->consumerMassTrx(1000, [1]);

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_activate_tron_address()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->activateTronAddress("TWoHLT3JkUB1gwF745JuQmtcJ6rZ712345");

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_consumer_reset_validity()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->consumerResetValidity(1);

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_consumer_summary()
    {
        $mockResponse  = [
            "status" => true,
            'data' => [
                'balance' => 7.890411,
                'credit_limit' => 1000,
                'total_count' => 1,
                'total_energy_consumption' => 95000,
                'total_received_energy' => 92535,
                'active_count' => 0,
                'active_energy_consumption' => 0,
                'normal_energy_unit_price' => 0.00042,
                'trenergy_energy_unit_price' => 0.0001,
                'aml_price_usd' => 1,
                'daily_expenses_avg' => 316.10543333,
                'period_prices_sun' => [
                    '15' => 100,
                    '60' => 100,
                    '480' => 127,
                    '1440' => 127
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->consumerSummary();

        $this->assertInstanceOf(ConsumerSummaryDTO::class, $result);
    }

    public function test_consumer_address_report()
    {
        $mockResponse  = [
            "status" => true,
            'data' => [
                "total_paid_trx" => 13
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->consumerAddressReport(
            "TWoHLT3JkUB1gwF745JuQmtcJ6rZ712345",
            '2023-02-02',
            '2025-01-01'
        );

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_consumer_consamption_stats()
    {
        $mockResponse  = [
            "status" => true,
            'data' => [
                'items' => [
                    [
                        'date' => '2024-09-27',
                        'resource_amount' => 225000.0,
                        'trx_price' => 22.5
                    ],
                    [
                        'date' => '2024-09-26',
                        'resource_amount' => 4583162.5,
                        'trx_price' => 458.3163
                    ]
                ],
                'total_trx_price' => 480.8163,
                'total_resource_amount' => 4808162.5,
                'total_energy_balance_expenses' => 0.0
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->consumersConsumptionStats('2020-01-01', '2025-01-01', 3);

        $this->assertInstanceOf(ConsumptionStatTotalDTO::class, $result);
        $this->assertInstanceOf(ConsumptionStatDTO::class, $result->items[0]);
    }

    public function test_consumer_mass_payment_period_exception()
    {
        $this->expectException(InvalidArgumentException::class);

        $mockResponse  = [
            "status" => true,
            'data' => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $this->trenergyService->consumerMassPaymentPeriod([1], 123, true);
    }

    public function test_consumer_mass_payment_period()
    {
        $mockResponse  = [
            "status" => true,
            'data' => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->consumerMassPaymentPeriod([1], 15, true);

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_aml_list()
    {
        $mockResponse = [
            'status' => true,
            'data' => [
                [
                    'address' => 'TUL2KAA34K6W3bDsGb9S1QLVEqTKHj1234',
                    'txid' => null,
                    'context' => [
                        'pending' => false,
                        'entities' => [
                            [
                                'level' => 'MEDIUM_RISK',
                                'entity' => 'EXCHANGE_UNLICENSED',
                                'riskScore' => 0.052
                            ],
                            [
                                'level' => 'HIGH_RISK',
                                'entity' => 'SANCTIONS',
                                'riskScore' => 0.023
                            ],
                            [
                                'level' => 'MEDIUM_RISK',
                                'entity' => 'GAMBLING',
                                'riskScore' => 0.004
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'PAYMENT',
                                'riskScore' => 0.006
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'EXCHANGE_LICENSED',
                                'riskScore' => 0.889
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'OTHER',
                                'riskScore' => 0.012
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'WALLET',
                                'riskScore' => 0.012
                            ]
                        ],
                        'riskScore' => 0.297
                    ]
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->amlList('2020-05-05', '2025-05-05', 12);

        $this->assertInstanceOf(AmlListDTO::class, $result->first());
        $this->assertInstanceOf(AmlListContextDTO::class, $result->first()->context);
    }

    public function test_aml_check_invalid_argument_blockchain()
    {
        $this->expectException(InvalidArgumentException::class);

        $mockResponse  = [
            "status" => true,
            'data' => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $this->trenergyService->amlCheck('qwe', 'TGhTsWMhPRagThYHkbNhFfvrF2RF3peN5e');
    }

    public function test_aml_check_invalid_argument_address_txid()
    {
        $this->expectException(InvalidArgumentException::class);

        $mockResponse  = [
            "status" => true,
            'data' => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $this->trenergyService->amlCheck('tron');
    }

    public function test_aml_check_invalid_argument_btc_address()
    {
        $this->expectException(InvalidArgumentException::class);

        $mockResponse  = [
            "status" => true,
            'data' => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $this->trenergyService->amlCheck('btc', txid: 'smth');
    }

    public function test_aml_check()
    {
        $mockResponse = [
            'status' => true,
            'data' => [
                [
                    'address' => 'TUL2KAA34K6W3bDsGb9S1QLVEqTKHj1234',
                    'txid' => null,
                    'context' => [
                        'pending' => false,
                        'entities' => [
                            [
                                'level' => 'MEDIUM_RISK',
                                'entity' => 'EXCHANGE_UNLICENSED',
                                'riskScore' => 0.052
                            ],
                            [
                                'level' => 'HIGH_RISK',
                                'entity' => 'SANCTIONS',
                                'riskScore' => 0.023
                            ],
                            [
                                'level' => 'MEDIUM_RISK',
                                'entity' => 'GAMBLING',
                                'riskScore' => 0.004
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'PAYMENT',
                                'riskScore' => 0.006
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'EXCHANGE_LICENSED',
                                'riskScore' => 0.889
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'OTHER',
                                'riskScore' => 0.012
                            ],
                            [
                                'level' => 'LOW_RISK',
                                'entity' => 'WALLET',
                                'riskScore' => 0.012
                            ]
                        ],
                        'riskScore' => 0.297
                    ]
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->amlCheck('btc', 'TGhTsWMhPRagThYHkbNhFfvrF2RF3peN5e',  txid: 'smth');

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_get_withdrawals_dto()
    {
        $mockResponse = [
            'status' => true,
            'data' => [
                [
                    "id" => 1,
                    "trx_amount" => 100,
                    "status" => "Completed",
                    "address" => "TMP3f4UtGBc3dMAj7eA2afzyULaehN3uhZ",
                    "created_at" => "30-06-2025 06:10:05",
                    "updated_at" => "30-06-2025 06:10:49"
                ], [
                    "id" => 2,
                    "trx_amount" => 150,
                    "status" => "Completed",
                    "address" => "TMP3f4UtGBc3dMAj7eA2afzyULaehN3uhZ",
                    "created_at" => "03-06-2025 07:54:04",
                    "updated_at" => "03-06-2025 07:54:34"
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->getWithdrawals();

        $this->assertInstanceOf(GetWithdrawalsDTO::class, $result->first());
    }

    public function test_withdrawals()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->withdrawals(1, 'TY3dRk4eQ75dCrW7tUcCzggU9rnz4V1111', '123456' );

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_stakes()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                ["id" => 1,
                "trx_amount" => 100000,
                "type" => 1,
                "is_closes" => false,
                "closes_at" => null,
                "created_at" => "14-07-2023",
                "available_at" => "14-07-2023",
                "next_reward_at" => "15-07-2025"],
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->stakes();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(GetStakeDTO::class, $result->first());
    }

    public function test_stake()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->stake(1234);

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_unstake()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                "unstake_date" => "2023-05-31"
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->unstake(1234, 123456);

        $this->assertInstanceOf(ArrayDTO::class, $result);
        $this->assertEquals('2023-05-31', $result->unstake_date);
    }

    public function test_stake_sync()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->stakeSync();

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_stake_profitability()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                [
                    "received" => 2500,
                    "date" => "06-2023"
                ],
                [
                    "received" => 1500,
                    "date" => "07-2023"
                ],
                [
                    "received" => 1000,
                    "date" => "08-2023"
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->stakeProfitability();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(StakeProfitabilityDTO::class, $result->first());
    }

    public function test_stake_profitability_week()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                [
                    "received" => 2500,
                    "date" => "06-2023"
                ],
                [
                    "received" => 1500,
                    "date" => "07-2023"
                ],
                [
                    "received" => 1000,
                    "date" => "08-2023"
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->stakeProfitability(StakeProfitabilityPeriodEnum::week->value);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(StakeProfitabilityDTO::class, $result->first());
    }

    public function test_stake_profitability_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $mockResponse  = [
            "status" => true,
            "data" => [
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $this->trenergyService->stakeProfitability(1234);
    }

    public function test_partners()
    {
        $mockResponse = [
            'status' => true,
            'data' => [
                [
                    'line' => 3,
                    'users' => [
                        [
                            'id' => 32,
                            'name' => 'Пользователь 550831',
                            'photo' => 'http://localhost:8081/img/default-avatar-1.png',
                            'leader_level' => 0,
                            'level_name' => '-',
                            'leader_id' => 20,
                            'stake' => 0,
                            'reactors_count' => 0,
                            'active_stakers_count' => 0,
                            'total_stakes_in_structure' => 0,
                            'total_reactors_in_structure' => 0,
                            'total_active_stakers_in_structure' => 0,
                            'total_partners_in_structure' => 0
                        ],
                        [
                            'id' => 35,
                            'name' => 'Пользователь 555939',
                            'photo' => 'http://localhost:8081/img/default-avatar-1.png',
                            'leader_level' => 0,
                            'level_name' => '-',
                            'leader_id' => 20,
                            'stake' => 0,
                            'reactors_count' => 0,
                            'active_stakers_count' => 0,
                            'total_stakes_in_structure' => 0,
                            'total_reactors_in_structure' => 0,
                            'total_active_stakers_in_structure' => 0,
                            'total_partners_in_structure' => 0
                        ]
                    ]
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->partners(1);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(StructureDTO::class, $result->first());
        $this->assertInstanceOf(PartnerDTO::class, $result->first()->users[0]);
    }

    public function test_wallets()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                [
                    "id" => 312,
                    "address" => "TMojfiq4wMLxnf7uuWYqfbsHjgTP2gpsto",
                    "created_at" => "28-09-2023 13:31:57",
                    "updated_at" => "03-03-2025 07:47:01"
                ],
                [
                    "id" => 308,
                    "address" => "TPU1wKoV5kWf7wjNUmfKqYJfo6gCriggKQ",
                    "created_at" => "27-09-2023 14:44:16",
                    "updated_at" => "18-06-2025 13:10:04"
                ]
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->getWallets();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(WalletDTO::class, $result->first());
    }

    public function test_add_wallet()
    {
        $mockResponse  = [
            "status" => true,
            "data" => [
                'id' => 1
            ]
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->addWallet('Some Address');

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_drop_wallet()
    {
        $mockResponse  = [
            "status" => true,
            "data" => []
        ];

        $this->trenergyService->method('sendGetContent')
            ->willReturn(json_encode($mockResponse));

        $result = $this->trenergyService->dropWallet(1, '1234567');

        $this->assertInstanceOf(ArrayDTO::class, $result);
    }

    public function test_private_function_result()
    {
        $method = (new ReflectionClass($this->trenergyService))->getMethod('result');
        $method->setAccessible(true);

        $result = $method->invoke($this->trenergyService,ArrayDTO::class, [] );

        $this->assertIsArray($result);
    }

    public function test_private_function_date_argument_validation()
    {
        $this->expectException(InvalidArgumentException::class);
        $method = (new ReflectionClass($this->trenergyService))->getMethod('dateArgumentValidation');
        $method->setAccessible(true);

        $result = $method->invoke($this->trenergyService,'some string but not date' );
    }


}
