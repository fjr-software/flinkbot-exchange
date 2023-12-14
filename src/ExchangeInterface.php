<?php

declare(strict_types=1);

namespace FjrSoftware\Flinkbot\Exchange;

interface ExchangeInterface
{
    public function getAccountInformation(): array;

    public function getStaticsTicker(string $symbol): array;

    public function createOrder(array $data): array;

    public function cancelOrder(string $symbol, string $orderId): array;

    public function getOrders(string $symbol): array;

    public function getBook(string $symbol): array;

    public function getCandles(string $symbol, string $interval, int $limit): array;

    public function getPosition(string $symbol): array;

    public function closePosition(string $symbol, string $side): array;
}
