<?php

namespace FjrSoftware\Flinkbot\Exchange;

interface ExchangeInterface
{
    /**
     * Get exchange information
     *
     * @return array
     */
    public function getExchangeInfo(): array;

    /**
     * Get account information
     *
     * @return array
     */
    public function getAccountInformation(): array;

    /**
     * Get statics ticker
     *
     * @param string $symbol
     * @return array
     */
    public function getStaticsTicker(string $symbol): array;

    /**
     * Create order
     *
     * @param array $data
     * @return array
     */
    public function createOrder(array $data): array;

    /**
     * Cancel order
     *
     * @param string $symbol
     * @param string $orderId
     * @return array
     */
    public function cancelOrder(string $symbol, string $orderId): array;

    /**
     * Get order by id
     *
     * @param int $orderId
     * @param string $symbol
     * @return array
     */
    public function getOrderById(int $orderId, string $symbol): array;

    /**
     * Get orders
     *
     * @param string $symbol
     * @return array
     */
    public function getOrders(string $symbol): array;

    /**
     * Get open orders
     *
     * @param string $symbol
     * @return array
     */
    public function getOpenOrders(string $symbol): array;

    /**
     * Get book
     *
     * @param string $symbol
     * @return array
     */
    public function getBook(string $symbol): array;

    /**
     * Get candles
     *
     * @param string $symbol
     * @param string $interval
     * @param int $limit
     * @return array
     */
    public function getCandles(string $symbol, string $interval, int $limit): array;

    /**
     * Get position
     *
     * @param string $symbol
     * @return array
     */
    public function getPosition(string $symbol): array;

    /**
     * Close position
     *
     * @param string $symbol
     * @param string $side
     * @param float $price
     * @param bool $stop
     * @return array
     */
    public function closePosition(string $symbol, string $side, float $price, bool $stop = false): array;

    /**
     * Get close price from candles
     *
     * @param array $candles
     * @return array
     */
    public function getClosePrice(array $candles): array;

    /**
     * Get current value
     *
     * @param array $data
     * @param string|null $key
     * @return float
     */
    public function getCurrentValue(array $data, ?string $key = null): float;

    /**
     * Calculate percentage
     *
     * @param float $value1
     * @param float $value2
     * @return float
     */
    public function percentage(float $value1, float $value2): float;

    /**
     * Calculate profit
     *
     * @param float $value
     * @param float $percentage
     * @return float
     */
    public function calculeProfit(float $value, float $percentage): float;

    /**
     * Check if order is time box
     *
     * @param int $orderTime
     * @param int $timeout
     * @return bool
     */
    public function isTimeBoxOrder(int $orderTime, int $timeout): bool;

    /**
     * Format decimal
     *
     * @param float $base
     * @param float $value
     * @return float
     */
    public function formatDecimal(float $base, float $value): float;
}
