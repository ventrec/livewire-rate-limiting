<?php

use DanHarrin\LivewireRateLimiting\Attributes\RateLimit;
use Livewire\Volt\Component;

new class extends Component {
    public int $counter = 0;

    #[RateLimit(maxAttempts: 2)]
    public function increment(): void
    {
        $this->counter++;
    }

    #[RateLimit(maxAttempts: 2, validationErrors: ['firstname' => 'Too many requests.'])]
    public function incrementWithValidationErrors(): void
    {
        $this->counter++;
    }
}; ?>

<div>
    //
</div>
