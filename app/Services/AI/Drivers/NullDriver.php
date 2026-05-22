<?php

namespace App\Services\AI\Drivers;

use App\Services\AI\Contracts\AiDriver;

/**
 * Offline, deterministic driver for local dev, CI and tests. It never makes a
 * network call, so the platform is fully functional (and the AI features
 * exercise their full code path) without an API key.
 */
class NullDriver implements AiDriver
{
    public function complete(string $prompt, array $options = []): string
    {
        // Deterministic pseudo-score so lead scoring etc. produce stable output.
        $seed = crc32($prompt) % 100;

        return json_encode([
            'stub'   => true,
            'score'  => $seed,
            'answer' => 'AI is in offline mode (AI_DRIVER=null). Set ANTHROPIC_API_KEY to enable live responses.',
        ]);
    }
}
