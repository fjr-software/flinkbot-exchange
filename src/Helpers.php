<?php

declare(strict_types=1);

namespace FjrSoftware\Flinkbot\Exchange;

use DateTime;

trait Helpers
{
    /**
     * Get high price from candles
     *
     * @param array $candles
     * @return array
     */
    public function getHighPrice(array $candles): array
    {
        return array_map(fn($candle) => $candle['high'], $candles);
    }

    /**
     * Get low price from candles
     *
     * @param array $candles
     * @return array
     */
    public function getLowPrice(array $candles): array
    {
        return array_map(fn($candle) => $candle['low'], $candles);
    }

    /**
     * Get close price from candles
     *
     * @param array $candles
     * @return array
     */
    public function getClosePrice(array $candles): array
    {
        return array_map(fn($candle) => $candle['close'], $candles);
    }

    /**
     * Get current value
     *
     * @param array $data
     * @param string|null $key
     * @return float
     */
    public function getCurrentValue(array $data, ?string $key = null): float
    {
        if ($key !== null) {
            $data = array_column($data, $key);
        }

        return (float) (end($data) ?? 0);
    }

    /**
     * Calculate percentage
     *
     * @param float $value1
     * @param float $value2
     * @return float
     */
    public function percentage(float $value1, float $value2): float
    {
        if (!$value1 || !$value2) {
            return 0;
        }

        $dif = bcsub((string) $value1, (string) $value2, 5);
        $div = bcdiv($dif, (string) $value1, 5);

        return (float) bcmul($div, '100', 2);
    }

    /**
     * Calculate profit
     *
     * @param float $value
     * @param float $percentage
     * @return float
     */
    public function calculeProfit(float $value, float $percentage): float
    {
        $percentage = bcdiv((string) $percentage, '100', 4);
        return (float) bcmul((string) $value, (string) $percentage, 2);
    }

    /**
     * Check if order is time box
     *
     * @param int $orderTime
     * @param int $timeout
     * @return bool
     */
    public function isTimeBoxOrder(int $orderTime, int $timeout): bool
    {
        $timeOrder = new DateTime('@'. (int) ($orderTime / 1e3));
        $timeNow = new DateTime('now');
        $time = $timeOrder->diff($timeNow);

        $timeBox = (int) ($time->format('%d')) * 86400;
        $timeBox += (int) ($time->format('%h')) * 3600;
        $timeBox += (int) ($time->format('%i')) * 60;
        $timeBox += (int) ($time->format('%s'));

        return $timeBox >= $timeout;
    }

    /**
     * Format decimal
     *
     * @param float $base
     * @param float $value
     * @return float
     */
    public function formatDecimal(float $base, float $value): float
    {
        preg_match('/\.([0-9]+)/', (string) $base, $temp);
        $decimal = strlen($temp[1] ?? '');
        $value = $decimal ? round($value, $decimal) : ceil($value);

        return (float) $value;
    }
}
