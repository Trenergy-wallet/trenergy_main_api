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
composer require apd/trenergy
php artisan vendor:publish --provider="Apd\Trenergy\Providers\TrenergyServiceProvider" --tag="config"
```

## ⚙️ Configuration

Add to your .env:
```dotenv
TRENERGY_API_URL=https://api.trenergy.com
TRENERGY_API_KEY=your_api_key_here
TRENERGY_API_LANG=en  # Supported: en, ru
```

## 💻 Basic Usage

```php
use Apd\Trenergy\Facades\Trenergy;
use Apd\Trenergy\Enums\Consumers\PaymentPeriod;

// Get account info
$account = Trenergy::getAccount();

// Create consumer
$order = Trenergy::createConsumer(
    paymentPeriod: PaymentPeriod::fifteenMinutes->value,
    address: 'TXYZ...',
    resourceAmount: 100.0,
    name: 'My Consumer',
    autoRenewal: true
);
```

## 🔧 Available Methods

**Account Management**

| Method | Return Type  |Description |
|:-------------|:---------------:|-----------------------:|
| getAccount() |   AccountDTO    | Get authenticated account |
| getTopUp()   | AccountTopUpDTO |    Get top-up information |

**Consumers Management**

| Method                   |       Return Type       |           Description |
|:-------------------------|:-----------------------:|----------------------:|
| getConsumers()           | Collection<ConsumerDTO> |    List all consumers |
| getConsumer(int $id)     |       ConsumerDTO       | Get specific consumer |
| activateConsumer(int $id)|        ArrayDTO         |    Activate consumer  |
		
		
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

```php
use Apd\Trenergy\Exceptions\TrenergyWrongPaymentPeriod;

try {
    Trenergy::createConsumer(...);
} catch (TrenergyWrongPaymentPeriod $e) {
    // Handle invalid payment period
    $allowedPeriods = $e->getMessage(); 
} catch (\Exception $e) {
    // Handle other errors
}
```
