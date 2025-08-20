<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\Services;

use Apd\Trenergy\Services\BaseService;
use Apd\Trenergy\Services\TrenergyConnector;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class BaseServiceSendGetContentTest extends TestCase
{
    private BaseService $baseService;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Guzzle mock handler
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        // Create connector mock
        $connectorMock = $this->createMock(TrenergyConnector::class);
        $connectorMock->method('getBaseUrl')->willReturn('https://api.example.com');
        $connectorMock->method('getApiKey')->willReturn('test-api-key');
        $connectorMock->method('getCommonHeaders')->willReturn([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);

        // Create testable service instance
        $this->baseService = $this->getMockBuilder(BaseService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createClient'])
            ->getMock();

        $this->baseService->method('createClient')->willReturn($guzzleClient);

        // Set protected properties using reflection
        $this->setProtectedProperty($this->baseService, 'connector', $connectorMock);
        $this->setProtectedProperty($this->baseService, 'headers', [
            'Authorization' => 'Bearer test-api-key',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);
    }

    public function test_create_client(): void
    {
        // Создаем экземпляр BaseService без моков
        $service = new class extends BaseService {
            public function publicCreateClient()
            {
                return $this->createClient();
            }
        };

        $client = $service->publicCreateClient(); // Прямой вызов
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_successful_get_request(): void
    {
        // Mock successful response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['success' => true, 'data' => 'test'])
        ));

        $this->setProtectedProperty($this->baseService, 'endPoint', 'test');
        $this->setProtectedProperty($this->baseService, 'method', 'GET');

        $result = $this->callProtectedMethod($this->baseService, 'sendGetContent');

        // Декодируем JSON строку в массив для сравнения
        $decodedResult = json_decode($result, true);

        $this->assertEquals(['success' => true, 'data' => 'test'], $decodedResult);
    }

    public function test_unsuccessful_get_request()
    {
        // Mock successful response
        $this->mockHandler->append(new Response(
            500,
            ['Content-Type' => 'application/json'],
            json_encode(['success' => false, 'error' => 'test'])
        ));

        $this->setProtectedProperty($this->baseService, 'endPoint', 'test');
        $this->setProtectedProperty($this->baseService, 'method', 'GET');

        $result = $this->callProtectedMethod($this->baseService, 'sendGetContent');

        $this->assertEquals(['success' => false, 'error' => 'test'], $result);
    }

    public function test_unsuccessful_get_request_no_error_body()
    {
        // Mock successful response
        $this->mockHandler->append(new Response(
            500,
            ['Content-Type' => 'application/json']
        ));

        $this->setProtectedProperty($this->baseService, 'endPoint', 'test');
        $this->setProtectedProperty($this->baseService, 'method', 'GET');

        $result = $this->callProtectedMethod($this->baseService, 'sendGetContent');

        $this->assertEquals(
            'Server error: `GET https://api.example.comtest` resulted in a `500 Internal Server Error` response',
            $result
        );
    }

    /**
     * @throws ReflectionException|\ReflectionException
     */
    private function setProtectedProperty(object $object, string $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * @throws ReflectionException
     */
    private function callProtectedMethod(object $object, string $method, array $args = [])
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }
}
