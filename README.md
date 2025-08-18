# Trenergy API

[![Latest Version](https://img.shields.io/packagist/v/apd/trenergy.svg?style=flat-square)](https://packagist.org/packages/apd/trenergy)
[![License](https://img.shields.io/packagist/l/apd/trenergy.svg?style=flat-square)](https://packagist.org/packages/apd/trenergy)
[![PHP Version](https://img.shields.io/packagist/php-v/apd/trenergy.svg?style=flat-square)](https://php.net)

Official PHP client for Trenergy API with full Laravel integration.

## 📦 Requirements

- PHP 8.2+
- Laravel 9.x or 10.x
- GuzzleHTTP 7.0+

## 🚀 Installation

```bash
composer config repositories.trenergy-main-api vcs https://github.com/Trenergy-wallet/trenergy_main_api
composer install
php artisan vendor:publish --provider="Apd\Trenergy\Providers\TrenergyServiceProvider" --tag="config"
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

### Account Methods

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `getAccount()` | - | `AccountDTO|array` | Get user account information |
| `getTopUp()` | - | `AccountTopUpDTO|array` | Get top-up address and QR code |
| `subscribe()` | `bool $isBalanceUsed`, `?bool $isCredit` | `SubscribeDTO|array` | Create subscription |

### Consumer Methods

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `getConsumers()` | - | `Collection<ConsumerDTO>|array` | List all consumers |
| `createConsumer()` | `string $paymentPeriod`, `string $address`, `float $resourceAmount`, `string $name`, `int $autoRenewal=0` | `OrderCreatedDTO|array` | Create new consumer |
| `getConsumer()` | `int $consumerId` | `ConsumerDTO|array` | Get consumer details |
| `activateConsumer()` | `int $consumerId` | `ArrayDTO|array` | Activate consumer |
| `deActivateConsumer()` | `int $consumerId` | `ArrayDTO|array` | Deactivate consumer |
| `updateConsumer()` | `int $consumerId`, `float $resourceAmount`, `int $paymentPeriod`, `bool $autoRenewal`, `?string $name` | `ConsumerDTO|array` | Update consumer |
| `destroyConsumer()` | `int $consumerId` | `ArrayDTO|array` | Delete consumer |
| `buyEnergy()` | `string $paymentPeriod`, `string $address`, `float $resourceAmount`, `string $name`, `int $autoRenewal=0` | `ArrayDTO|array` | Create and activate consumer |

### Wallet Methods

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `getWallets()` | - | `Collection<WalletDTO>|array` | List all wallets |
| `addWallet()` | `string $address` | `ArrayDTO|array` | Add new wallet |
| `dropWallet()` | `int $walletId`, `string $oneTimePassword` | `ArrayDTO|array` | Remove wallet |

### Stake Methods

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `stakes()` | `int $perPage=5` | `GetStakeDTO|array` | Get stake list |
| `stake()` | `float $trxAmount` | `ArrayDTO|array` | Create new stake |
| `unstake()` | `float $trxAmount`, `string $oneTimePassword` | `ArrayDTO|array` | Unstake funds |
| `stakeProfitability()` | `?int $period` | `StakeProfitabilityDTO|array` | Get profitability stats |

### AML Methods

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `amlList()` | `?string $fromDate`, `?string $toDate`, `?int $perPage` | `AmlListDTO|array` | Get AML records |
| `amlCheck()` | `string $blockchain`, `?string $address`, `?string $txid` | `ArrayDTO|array` | Check AML status |
		
		
## 🏗 DTO Structure

All responses are type-hinted DTOs:
```php 
$account = Trenergy::getAccount();
echo $account->name; // string
echo $account->energy_balance; // float

$consumer = Trenergy::getConsumer(123);
echo $consumer->order->status; // Access nested OrderDTO
```

## 🚨 Error Handling

All methods may throw:
- `TrenergyServerErrorException`
- `TrenergyWrongPaymentPeriod`
- `RequireEnvParameters`

