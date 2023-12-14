<?php

declare(strict_types=1);

namespace FjrSoftware\Flinkbot\Exchange;

use FjrSoftware\Flinkbot\Request\Request;
use Psr\Http\Message\ResponseInterface;

class Binance implements ExchangeInterface
{
    public const URL_MAIN = 'https://fapi.binance.com';

    public const PATH_OLD = '/fapi/v1';

    public const PATH = '/fapi/v2';

    private Request $request;

    private RateLimit $rateLimit;

    public function __construct(
        private readonly string $publicKey,
        private readonly string $privateKey
    ) {
        $this->request = new Request(
            self::URL_MAIN,
            [
                'Content-Type' => 'application/json',
                'X-MBX-APIKEY' => $this->publicKey
            ]
        );
        $this->rateLimit = new RateLimit();

        $this->request->setCallbackRequest(function (ResponseInterface $response) {
            $orderCount = (int) ($response->getHeader('x-mbx-order-count-1m')[0] ?? 0);
            $requestCount = (int) ($response->getHeader('x-mbx-used-weight-1m')[0] ?? 0);

            $this->rateLimit
                ->setCurrentOrder($orderCount)
                ->setCurrentRequest($requestCount);
        });
    }

    public function getAccountInformation(): array
    {
        $response = $this->request->get(
            self::PATH.'/account',
            [
                'query' => $this->prepareData()
            ]
        );

        return $this->response($response);
    }

    public function getStaticsTicker(string $symbol): array
    {
        $response = $this->request->get(
            self::PATH_OLD.'/ticker/24hr',
            [
                'query' => $this->prepareData([
                    'symbol' => $symbol
                ])
            ]
        );

        return $this->response($response);
    }

    public function createOrder(array $data): array
    {
        $response = $this->request->post(
            self::PATH_OLD.'/order',
            [
                'query' => $this->prepareData($data)
            ]
        );

        return $this->response($response);
    }

    public function cancelOrder(string $symbol, string $orderId): array
    {
        $response = $this->request->delete(
            self::PATH_OLD.'/order',
            [
                'query' => $this->prepareData([
                    'symbol' => $symbol,
                    'orderID' => $orderId
                ])
            ]
        );

        return $this->response($response);
    }

    public function getOrders(string $symbol): array
    {
        $response = $this->request->get(
            self::PATH_OLD.'/allOrders',
            [
                'query' => $this->prepareData([
                    'symbol' => $symbol,
                    'limit' => 100
                ])
            ]
        );

        return $this->response($response);
    }

    public function getBook(string $symbol): array
    {
        $response = $this->request->get(
            self::PATH_OLD.'/depth',
            [
                'query' => $this->prepareData([
                    'symbol' => $symbol,
                    'limit' => 5
                ])
            ]
        );

        return $this->response($response);
    }

    public function getCandles(string $symbol, string $interval, int $limit): array
    {
        $response = $this->request->get(
            self::PATH_OLD.'/continuousKlines',
            [
                'query' => $this->prepareData([
                    'pair' => $symbol,
                    'contractType' => 'PERPETUAL',
                    'interval' => $interval,
                    'limit' => $limit
                ])
            ]
        );

        return $this->response($response);
    }

    public function getPosition(string $symbol): array
    {
        $response = $this->request->get(
            self::PATH.'/positionRisk',
            [
                'query' => $this->prepareData([
                    'symbol' => $symbol
                ])
            ]
        );

        return $this->response($response);
    }

    public function closePosition(string $symbol, string $side): array
    {
        return [];
    }

    private function response(ResponseInterface $response): array
    {
        $content = $response->getBody()->getContents();

        return json_decode($content, true) ?? [];
    }

    private function prepareData(array $data = []): array
    {
        $data['timestamp'] = time() * 1e3;
        $data['recvWindow'] = 6e4;
        $data['signature'] = $this->signature($data);

        return $data;
    }

    private function signature(array $data): string
    {
        return hash_hmac('sha256', http_build_query($data), $this->privateKey);
    }
}
