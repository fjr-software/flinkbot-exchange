<?php

namespace FjrSoftware\Flinkbot\Exchange;

interface ExchangeInterface
{
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
     * @param float $quantity
     * @param float $price
     * @return array
     */
    public function closePosition(string $symbol, string $side, float $quantity, float $price): array;
}
