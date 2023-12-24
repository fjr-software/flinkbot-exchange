<?php

declare(strict_types=1);

namespace FjrSoftware\Flinkbot\Exchange;

trait Helpers
{
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
     * @param string $key
     * @return float
     */
    public function getCurrentValue(array $data, string $key = null): float
    {
        if ($key) {
            $data = array_column($data, 'close');
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
        $dif = bcsub((string) $value1, (string) $value2, 5);
        $div = bcdiv($dif, (string) $value1, 5);

        return (float) bcmul($div, '100', 2);
    }
}
