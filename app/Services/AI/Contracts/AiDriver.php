<?php

namespace App\Services\AI\Contracts;

/**
 * The single seam every AI capability goes through. Swap implementations via
 * AI_DRIVER without touching a line of module code. Keeping this interface tiny
 * (one method) means new providers are trivial to add.
 */
interface AiDriver
{
    /**
     * Send a prompt and return the model's text completion.
     *
     * @param  array{system?:string,max_tokens?:int,temperature?:float}  $options
     */
    public function complete(string $prompt, array $options = []): string;
}
