# Trenergy API

## Table of Contents

<details>

   <summary>Contents</summary>

1. [📦 Requirements](#-requirements)
1. [🚀 Installation](#-installation)
1. [⚙️ Configuration](#-configuration)
1. [💻 Basic Usage example](#-basic-usage-example)
1. [🔧 Available Methods](#-available-methods)
1. [🚨 Error Handling](#-error-handling)

</details>

[![Latest Stable Version](https://poser.pugx.org/trenergy-wallet/trenergy_main_api/v)](https://packagist.org/packages/trenergy-wallet/trenergy_main_api)
[![Total Downloads](https://poser.pugx.org/trenergy-wallet/trenergy_main_api/downloads)](https://packagist.org/packages/trenergy-wallet/trenergy_main_api)
[![License](https://poser.pugx.org/trenergy-wallet/trenergy_main_api/license)](https://packagist.org/packages/trenergy-wallet/trenergy_main_api)
[![PHP Version](https://img.shields.io/packagist/php-v/trenergy-wallet/trenergy_main_api)](https://packagist.org/packages/trenergy-wallet/trenergy_main_api)

Official PHP client for Trenergy API with full Laravel integration.

## 📦 Requirements

- PHP 8.2+
- Laravel 9.x or 10.x
- GuzzleHTTP 7.0+

## 🚀 Installation

```bash
composer config repositories.trenergy-main-api vcs https://github.com/Trenergy-wallet/trenergy_main_api
composer require trenergy-wallet/api:^1.0.1

```

## ⚙️ Configuration

Add to your .env:
```dotenv
TRENERGY_API_URL=https://api.trenergy.com
TRENERGY_API_KEY=your_api_key_here
TRENERGY_API_LANG=en  # Supported: en, ru
```

## 💻 Basic Usage example

```php
use Apd\Trenergy\Facades\Trenergy;

// Get account info
$account = Trenergy::getAccount();

// Create consumer
$consumer = Trenergy::createConsumer(
    paymentPeriod: '15', 
    address: 'TXYZ...',
    resourceAmount: 100.0,
    name: 'My Consumer'
);

// Get wallets
$wallets = Trenergy::getWallets();
```

## 🔧 Available Methods

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `getAccount()` | - | `AccountDTO\|array` | Get account information |
| `getTopUp()` | - | `AccountTopUpDTO\|array` | Get top-up information |
| `subscribe()` | `bool $isBalanceUsed`, `?bool $isCredit = null` | `SubscribeDTO\|array` | Subscribe to service |
| `getConsumers()` | - | `Collection<ConsumerDTO>\|array` | List all consumers |
| `downLoadConsumerList()` | `?string $name`, `?bool $isActive`, `?bool $autoRenewal`, `?array $paymentPeriods`, `?array $wallet`, `?string $format` | `string` | Download consumer list |
| `createConsumer()` | `string $paymentPeriod`, `string $address`, `float $resourceAmount`, `string $name`, `int $autoRenewal = 0` | `OrderCreatedDTO\|array` | Create new consumer |
| `getConsumer()` | `int $consumerId` | `ConsumerDTO\|array` | Get consumer details |
| `activateConsumer()` | `int $consumerId` | `ArrayDTO\|array` | Activate consumer |
| `deActivateConsumer()` | `int $consumerId` | `ArrayDTO\|array` | Deactivate consumer |
| `updateConsumer()` | `int $consumerId`, `float $resourceAmount`, `int $paymentPeriod`, `bool $autoRenewal`, `?string $name = null` | `ConsumerDTO\|array` | Update consumer |
| `destroyConsumer()` | `int $consumerId` | `ArrayDTO\|array` | Delete consumer |
| `buyEnergy()` | `string $paymentPeriod`, `string $address`, `float $resourceAmount`, `string $name`, `int $autoRenewal = 0` | `ArrayDTO\|array` | Create and activate consumer |
| `toggleAutoRenewal()` | `bool $autoRenewal`, `array $consumersIds` | `ArrayDTO\|array` | Toggle auto-renewal |
| `consumerBlockchainEnergy()` | `int $consumerId` | `BlockchainEnergyDTO\|array` | Get blockchain energy |
| `consumerMassTrx()` | `float $amount`, `array $consumersIds` | `ArrayDTO\|array` | Mass TRX transfer |
| `activateTronAddress()` | `string $address` | `ArrayDTO\|array` | Activate TRON address |
| `consumerResetValidity()` | `int $consumerId` | `ArrayDTO\|array` | Reset validity period |
| `consumerSummary()` | - | `ConsumerSummaryDTO\|array` | Get consumer summary |
| `consumerAddressReport()` | `string $address`, `?string $fromDate`, `?string $toDate` | `ArrayDTO\|array` | Get address report |
| `consumersConsumptionStats()` | `string $fromDate`, `string $toDate`, `?int $perPage` | `ConsumptionStatTotalDTO\|array` | Get consumption stats |
| `consumerMassPaymentPeriod()` | `array $consumersIds`, `int $paymentPeriod`, `bool $autoRenewal` | `ArrayDTO\|array` | Mass update payment period |
| `amlList()` | `?string $fromDate`, `?string $toDate`, `?int $perPage` | `AmlListDTO\|array` | Get AML list |
| `amlCheck()` | `string $blockchain`, `?string $address`, `?string $txid` | `ArrayDTO\|array` | Check AML status |
| `amlWalletType()` | `string $address` | `AmlWalletTypeDTO\|array` | Check AML status |
| `amlShow()` | `int $amlId` | `AmlCheckResultDTO\|array` | Show AML |
| `amlRepeatDeclined()` | `int $amlId` | `ArrayDTO\|array` | Repeat AML when Declined |
| `amlDeleteById()` | `int $amlId` | `ArrayDTO\|array` | Delete AML Report |
| `getWithdrawals()` | `?int $perPage = 5` | `GetWithdrawalsDTO\|array` | Get withdrawals list |
| `withdrawals()` | `float $trxAmount`, `string $address`, `string $oneTimePassword` | `ArrayDTO\|array` | Create withdrawal |
| `stakes()` | `int $perPage = 5` | `GetStakeDTO\|array` | Get stakes list |
| `stake()` | `float $trxAmount` | `ArrayDTO\|array` | Create stake |
| `unstake()` | `float $trxAmount`, `$oneTimePassword` | `ArrayDTO\|array` | Unstake funds |
| `stakeSync()` | - | `ArrayDTO\|array` | Sync stakes |
| `stakeProfitability()` | `?int $period = null` | `StakeProfitabilityDTO\|array` | Get profitability stats |
| `partners()` | `?int $leaderId = null` | `StructureDTO\|array` | Get partners structure |
| `getWallets()` | - | `Collection<WalletDTO>\|array` | Get wallets list |
| `addWallet()` | `string $address` | `ArrayDTO\|array` | Add wallet |
| `dropWallet()` | `int $walletId`, `string $oneTimePassword` | `ArrayDTO\|array` | Remove wallet |
		
		

## 🚨 Error Handling

All methods may throw:
- `TrenergyServerErrorException`
- `TrenergyWrongPaymentPeriod`
- `RequireEnvParameters`

