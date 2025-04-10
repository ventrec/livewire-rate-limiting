<?php

namespace DanHarrin\LivewireRateLimiting\Attributes;

use Attribute;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Livewire\Features\SupportAttributes\Attribute as LivewireAttribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RateLimit extends LivewireAttribute
{
    use WithRateLimiting;

    public function __construct(
        private int $maxAttempts,
        private int $decaySeconds = 60,
        private array $validationErrors = [],
        private ?string $methodName = null,
    ) {
    }

    public function call($params, $returnEarly): void
    {
        rescue(function () {
            $this->rateLimit($this->maxAttempts, method: $this->methodName ?: $this->getName());
        }, function ($e) use ($returnEarly) {
            if (count($this->validationErrors) === 0) {
                throw $e;
            }

            $this->component->setErrorBag($this->validationErrors);
            $returnEarly();
        });
    }
}
