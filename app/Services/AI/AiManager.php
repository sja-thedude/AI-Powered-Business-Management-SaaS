<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AiDriver;
use App\Services\AI\Drivers\AnthropicDriver;
use App\Services\AI\Drivers\NullDriver;
use InvalidArgumentException;

/**
 * Application-facing AI gateway. Modules call high-level methods here
 * (scoreLead, draftInvoice, ask, ...) and never touch a vendor SDK. The
 * underlying driver is chosen from config/ai.php and is fully swappable.
 *
 * Bound as a singleton in AppServiceProvider.
 */
class AiManager
{
    protected AiDriver $driver;

    public function __construct()
    {
        $this->driver = $this->resolveDriver(config('ai.driver') ?: 'fake');
    }

    public function driver(): AiDriver
    {
        return $this->driver;
    }

    public function enabled(string $capability): bool
    {
        return (bool) config("ai.capabilities.{$capability}.enabled", false);
    }

    /** Free-form completion used by the assistant & chatbot capabilities. */
    public function ask(string $prompt, array $options = []): string
    {
        return $this->driver->complete($prompt, $options);
    }

    /**
     * AI sales/lead scoring: returns a 0–100 likelihood-to-convert score.
     * The prompt is structured so even the null driver yields a stable value.
     */
    public function scoreLead(array $context): int
    {
        $payload = json_encode($context, JSON_PRETTY_PRINT);

        $raw = $this->driver->complete(
            "Score this sales lead 0-100 for likelihood to convert. "
            ."Reply with ONLY a JSON object {\"score\": <int>}.\n\nLead:\n{$payload}",
            ['system' => 'You are a precise B2B sales analyst.', 'max_tokens' => 64],
        );

        $score = (int) (json_decode($raw, true)['score'] ?? 0);

        return max(0, min(100, $score));
    }

    protected function resolveDriver(string $name): AiDriver
    {
        return match ($name) {
            'anthropic'    => new AnthropicDriver(config('ai.drivers.anthropic')),
            'fake', 'null' => new NullDriver(),
            default        => throw new InvalidArgumentException("Unknown AI driver [{$name}]."),
        };
    }
}
