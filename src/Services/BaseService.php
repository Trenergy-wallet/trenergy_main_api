<?php
declare(strict_types=1);

namespace Apd\Trenergy\Services;

use Apd\Trenergy\DTO\ArrayDTO;
use Apd\Trenergy\DTO\BaseDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;

class BaseService
{
    protected string $endPoint;

    protected string $method;

    protected array $param = [];
    protected array $headers = [];

    protected TrenergyConnector $connector;

    public function __construct()
    {
        $this->connector = TrenergyConnector::getConnect();
        $this->headers = array_merge(
            ['Authorization' => 'Bearer ' . $this->connector->getApiKey()],
            $this->connector->getCommonHeaders()
        );
    }

    protected function createClient(): Client
    {
        return new Client();
    }

    protected function setEndPoint(string $endPoint): static
    {
        $this->endPoint = $endPoint;

        return $this;
    }

    protected function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    protected function setParams(string $bodyKey, array|string $params): static
    {
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                $this->param[$bodyKey][$key] = $param;
            }
        } else {
            $this->param[$bodyKey] = $params;
        }

        return $this;
    }

    protected function setHeaders(array $headers): static
    {
        foreach ($headers as $key => $header) {
            $this->headers[$key] = $header;
        }

        return $this;
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    protected function sendGetContent(): array|string
    {
        try {
            return $this->createClient()->request(
                $this->method ?? 'GET',
                $this->connector->getBaseUrl() . $this->endPoint,
                    array_merge(['headers' => $this->headers], $this->param)
            )->getBody()->getContents();
        } catch (ClientException|ServerException $e) {
            $response = $e->getResponse();
            $errorBody = json_decode($response->getBody()->getContents());

            if (is_object($errorBody)) {
                $errorBody = json_decode(json_encode($errorBody), true);
            }

            if ($errorBody) {
                return (new ArrayDTO($errorBody))->toArray();
            }

            return $e->getMessage();
        }
    }
}
