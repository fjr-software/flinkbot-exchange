<?php

declare(strict_types=1);

namespace FjrSoftware\Flinkbot\Exchange;

use FjrSoftware\Flinkbot\Request\Request;
use Psr\Http\Message\ResponseInterface;

class Binance implements ExchangeInterface
{
    use Helpers;

    /**
     * @const string
     */
    public const URL_MAIN = 'https://fapi.binance.com';

    /**
     * @const string
     */
    public const PATH_OLD = '/fapi/v1';

    /**
     * @const string
     */
    public const PATH = '/fapi/v2';

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var RateLimit
     */
    private RateLimit $rateLimit;

    /**
     * Constructor
     *
     * @param string $publicKey
     * @param string $privateKey
     */
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
            $orderCount = (int) ($response->getHeader('x-mbx-order-count-1m')[0] ?? $this->rateLimit->getCurrentOrder());
            $requestCount = (int) ($response->getHeader('x-mbx-used-weight-1m')[0] ?? $this->rateLimit->getCurrentRequest());

            $this->rateLimit
                ->setCurrentOrder($orderCount)
                ->setCurrentRequest($requestCount);
        });
    }

    /**
     * @inheritdoc
     */
    public function getRateLimit(): RateLimit
    {
        return $this->rateLimit;
    }

    /**
     * @inheritdoc
     */
    public function getExchangeInfo(): array
    {
        $response = $this->request->get(
            self::PATH_OLD.'/exchangeInfo',
            [
                'query' => $this->prepareData()
            ]
        );

        return $this->response($response);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getOrderById(int $orderId, string $symbol): array
    {
        $response = $this->request->get(
            self::PATH_OLD.'/order',
            [
                'query' => $this->prepareData([
                    'symbol' => $symbol,
                    'orderId' => $orderId
                ])
            ]
        );

        return $this->response($response);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getOpenOrders(string $symbol): array
    {
        $response = $this->request->get(
            self::PATH_OLD.'/openOrders',
            [
                'query' => $this->prepareData([
                    'symbol' => $symbol
                ])
            ]
        );

        return $this->response($response);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

        return $this->parseTicker($this->response($response));
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function closePosition(string $symbol, string $side, float $price, bool $stop = false): array
    {
        return $this->createOrder([
            'symbol' => $symbol,
            'side' => $side === 'LONG' ? 'SELL' : 'BUY',
            'positionSide' => $side,
            'type' => !$stop ? 'TAKE_PROFIT_MARKET' : 'STOP_MARKET',
            'closePosition' => 'true',
            'stopPrice' => $price,
            'timeInForce' => 'GTE_GTC'
        ]);
    }

    /**
     * Parse ticker
     *
     * @param array $data
     * @return array
     */
    private function parseTicker(array $data): array
    {
        return array_map([$this, 'parseCandleStick'], $data);
    }

    /**
     * Parse candle stick
     *
     * @param array $data
     * @return array
     */
    private function parseCandleStick(array $data): array
    {
        $key = [
            'open_time',
            'open',
            'high',
            'low',
            'close',
            'volume',
            'close_time',
            'quote_asset_volume',
            'number_of_trades',
            'taker_buy_base_asset_volume',
            'taker_buy_quote_asset_volume',
            'ignore'
        ];

        return array_combine($key, $data);
    }

    /**
     * Response
     *
     * @param ResponseInterface $response
     * @return array
     */
    private function response(ResponseInterface $response): array
    {
        $content = $response->getBody()->getContents();

        return json_decode($content, true) ?? [];
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    private function prepareData(array $data = []): array
    {
        $data['timestamp'] = time() * 1e3;
        $data['recvWindow'] = 6e4;
        $data['signature'] = $this->signature($data);

        return $data;
    }

    /**
     * Signature
     *
     * @param array $data
     * @return string
     */
    private function signature(array $data): string
    {
        return hash_hmac('sha256', http_build_query($data), $this->privateKey);
    }
}
