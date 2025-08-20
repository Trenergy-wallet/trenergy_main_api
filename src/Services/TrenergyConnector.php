<?php
declare(strict_types=1);

namespace Apd\Trenergy\Services;

use Apd\Trenergy\Exceptions\RequireEnvParameters;

class TrenergyConnector
{
    protected string $baseUrl;

    private static TrenergyConnector $instance;
    protected array $commonHeaders;
    protected string $apiKey;

    /**
     * @throws RequireEnvParameters
     */
    private function __construct()
    {
        if (is_null(config('trenergy.base-url')) || is_null(config('trenergy.api-key'))) {
            throw new RequireEnvParameters();
        }

        $this->baseUrl = config('trenergy.base-url');
        $this->apiKey = config('trenergy.api-key');
        $this->commonHeaders = config('trenergy.headers');
    }

    public static function getConnect(): TrenergyConnector
    {
        if (empty(self::$instance)) {
            self::$instance = new TrenergyConnector();
        }

        return self::$instance;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getCommonHeaders(): array
    {
        return $this->commonHeaders;
    }
}
