<?php

declare(strict_types=1);

namespace FjrSoftware\Flinkbot\Exchange;

class RateLimit
{
    private int $currentRequest = 0;
    private int $currentOrder = 0;

    public function setCurrentRequest(int $current): RateLimit
    {
        $this->currentRequest = $this->using($current, $this->currentRequest);

        return $this;
    }

    public function getCurrentRequest(): int
    {
        return $this->currentRequest;
    }

    public function setCurrentOrder(int $current): RateLimit
    {
        $this->currentOrder = $this->using($current, $this->currentOrder);

        return $this;
    }

    public function getCurrentOrder(): int
    {
        return $this->currentOrder;
    }

    private function using(int $current, int $previous): int
    {
        $using = 0;

        if ($current) {
            $using = $current - $previous;
        }

        return $using;
    }
}
