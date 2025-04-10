<?php

namespace DanHarrin\LivewireRateLimiting\Tests;

use DanHarrin\LivewireRateLimiting\Attributes\RateLimit;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Livewire\Livewire;
use Livewire\Volt\Volt;

class RateLimitAttributeTest extends TestCase
{
    /** @test */
    public function can_rate_limit_and_throw_basic_exception()
    {
        $this->expectException(TooManyRequestsException::class);

        Livewire::test(AttributeComponent::class)
            ->call('increment')
            ->assertSet('counter', 1)
            ->call('increment')
            ->assertSet('counter', 2)
            ->call('increment');
    }

    /** @test */
    public function can_display_errors_when_rate_limiting()
    {
        Livewire::test(AttributeComponent::class)
            ->call('incrementWithValidationErrors')
            ->assertSet('counter', 1)
            ->call('incrementWithValidationErrors')
            ->assertSet('counter', 2)
            ->call('incrementWithValidationErrors')
            ->assertSet('counter', 2)
            ->assertHasErrors(['firstname' => 'Too many requests.']);
    }

    /** @test */
    public function can_rate_limit_and_throw_basic_exception_volt()
    {
        $this->mountVolt();
        $this->expectException(TooManyRequestsException::class);

        Volt::test('volt-attribute-component')
            ->call('increment')
            ->assertSet('counter', 1)
            ->call('increment')
            ->assertSet('counter', 2)
            ->call('increment');
    }

    /** @test */
    public function can_display_errors_when_rate_limiting_volt()
    {
        $this->mountVolt();

        Volt::test('volt-attribute-component')
            ->call('incrementWithValidationErrors')
            ->assertSet('counter', 1)
            ->call('incrementWithValidationErrors')
            ->assertSet('counter', 2)
            ->call('incrementWithValidationErrors')
            ->assertSet('counter', 2)
            ->assertHasErrors(['firstname' => 'Too many requests.']);
    }

    protected function mountVolt()
    {
        Volt::mount([
            __DIR__ . '/views',
        ]);
    }
}

class AttributeComponent extends \Livewire\Component
{
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

    public function render()
    {
        return view('component');
    }
}
