<?php

declare(strict_types=1);

namespace FjrSoftware\Flinkbot\Exchange;

class RateLimit
{
    /**
     * @var int
     */
    private int $currentRequest = 0;

    /**
     * @var int
     */
    private int $currentOrder = 0;

    /**
     * Set current request
     *
     * @param int $current
     * @return RateLimit
     */
    public function setCurrentRequest(int $current): RateLimit
    {
        $this->currentRequest = $current;

        return $this;
    }

    /**
     * Get current request
     *
     * @return int
     */
    public function getCurrentRequest(): int
    {
        return $this->currentRequest;
    }

    /**
     * Set current order
     *
     * @param int $current
     * @return RateLimit
     */
    public function setCurrentOrder(int $current): RateLimit
    {
        $this->currentOrder = $current;

        return $this;
    }

    /**
     * Get current order
     *
     * @return int
     */
    public function getCurrentOrder(): int
    {
        return $this->currentOrder;
    }
}
