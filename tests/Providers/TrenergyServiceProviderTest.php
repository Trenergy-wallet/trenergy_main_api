<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\Providers;


use Apd\Trenergy\Facades\Trenergy;
use Apd\Trenergy\Providers\TrenergyServiceProvider;
use Apd\Trenergy\Services\TrenergyConnector;
use Apd\Trenergy\Services\TrenergyService;
use Orchestra\Testbench\TestCase;

class TrenergyServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [TrenergyServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Устанавливаем тестовые конфигурации
        $app['config']->set('trenergy.base-url', 'https://api.test.trenergy.com');
        $app['config']->set('trenergy.api-key', 'test-api-key');
        $app['config']->set('trenergy.headers', [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
    }

    /** @test */
    public function it_provides_correct_accessor()
    {
        // Проверяем accessor через рефлексию
        $class = new \ReflectionClass(Trenergy::class);
        $method = $class->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $accessor = $method->invoke(null);

        $this->assertEquals('trenergy.service', $accessor);
    }

    /** @test */
    public function it_registers_trenergy_service_as_singleton()
    {
        // Получаем экземпляр сервиса из контейнера
        $service1 = $this->app->make('trenergy.service');
        $service2 = $this->app->make('trenergy.service');

        // Проверяем что это действительно TrenergyService
        $this->assertInstanceOf(TrenergyService::class, $service1);
        $this->assertInstanceOf(TrenergyService::class, $service2);

        // Проверяем что это один и тот же экземпляр (singleton)
        $this->assertSame($service1, $service2);
    }

    /** @test */
    public function it_get_base_url_base_service()
    {
        $connector = TrenergyConnector::getConnect();
        $this->assertEquals(config('trenergy.base-url'), $connector->getBaseUrl());
    }
}
