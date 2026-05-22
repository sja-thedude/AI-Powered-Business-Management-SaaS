<?php

namespace App\Services\AI\Drivers;

use App\Services\AI\Contracts\AiDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Live driver backed by the Anthropic Messages API (Claude). Thin on purpose:
 * one HTTP call, sane defaults, and the model is configurable per environment.
 */
class AnthropicDriver implements AiDriver
{
    public function __construct(protected array $config)
    {
    }

    public function complete(string $prompt, array $options = []): string
    {
        $response = Http::withHeaders([
            'x-api-key'         => $this->config['key'],
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(60)->post("{$this->config['base_url']}/messages", [
            'model'      => $this->config['model'],
            'max_tokens' => $options['max_tokens'] ?? $this->config['max_tokens'],
            'system'     => $options['system'] ?? 'You are an assistant inside the NovaBiz AI business platform.',
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Anthropic request failed: '.$response->body());
        }

        return $response->json('content.0.text', '');
    }
}
