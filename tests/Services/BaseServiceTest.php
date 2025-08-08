<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\Services;

use Apd\Trenergy\DTO\ArrayDTO;
use Apd\Trenergy\Services\BaseService;
use Apd\Trenergy\Services\TrenergyConnector;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use Orchestra\Testbench\TestCase;
use ReflectionClass;

class BaseServiceTest extends TestCase
{
    /** @test */
    public function it_test_set_protected_methods()
    {
        $service = new class extends BaseService {
            public  function publicSetEndPoint($endPoint)
            {
                return $this->setEndPoint($endPoint);
            }

            public function getEndPoint(): string
            {
                return $this->endPoint;
            }

            public function publicSetMethod(string $method)
            {
                return $this->setMethod($method);
            }

            public function getMethod()
            {
                return $this->method;
            }

            public function publicSetParams(string $bodyKey, array|string $params)
            {
                return $this->setParams($bodyKey, $params);
            }

            public function getParams()
            {
                return $this->param;
            }

            public function publicSetHeaders(array $headers)
            {
                return $this->setHeaders($headers);
            }

            public function getHeaders()
            {
                return $this->headers;
            }
        };



        $service->publicSetEndPoint('some_end_point');
        $service->publicSetMethod('some_method');
        $service->publicSetHeaders(['header1' => 'value1']);
        $service->publicSetParams('body', ['param1' => 'val1']);

        $this->assertEquals('some_end_point', $service->getEndPoint());
        $this->assertEquals('some_method', $service->getMethod());

        $this->assertEquals([
            "Authorization" => "Bearer test-api-key",
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "header1" => "value1"
        ], $service->getHeaders());

        $this->assertEquals(["body" => ["param1" => "val1"]], $service->getParams());

        $service->publicSetParams('json', 'some_json');

        $this->assertEquals(["body" => ["param1" => "val1"], "json" => "some_json"], $service->getParams());
    }
}
