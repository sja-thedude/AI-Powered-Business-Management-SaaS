<?php

/*
|--------------------------------------------------------------------------
| AI Infrastructure
|--------------------------------------------------------------------------
|
| NovaBiz AI ships "AI-ready": a single, swappable gateway (App\Services\AI)
| fronts every AI capability so modules never talk to a vendor SDK directly.
| Set AI_DRIVER=null during development to use the deterministic fake driver
| (no network, no key) or AI_DRIVER=anthropic in production.
|
| Capabilities below map 1:1 to the advanced features on the roadmap. Each
| is queued (see App\Jobs\AI) so heavy work never blocks a web request.
|
*/

return [

    // Note: avoid the literal value "null" here — Laravel's env() coerces the
    // string "null" to PHP null. Use "fake" for the offline driver.
    'driver' => env('AI_DRIVER', 'fake'),

    'drivers' => [
        'anthropic' => [
            'key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-opus-4-7'),
            'base_url' => 'https://api.anthropic.com/v1',
            'max_tokens' => 2048,
        ],
        'fake' => [
            // Deterministic, offline responses for local dev & tests.
        ],
    ],

    // Dedicated queue so AI workloads scale independently of transactional jobs.
    'queue' => env('AI_QUEUE', 'ai'),

    /*
     * Roadmap capabilities. `enabled` flags let us dark-launch features and
     * gate them per subscription plan without code changes.
     */
    'capabilities' => [
        'assistant'            => ['enabled' => true,  'label' => 'AI Employee Assistant'],
        'sales_prediction'     => ['enabled' => true,  'label' => 'AI Sales Prediction'],
        'invoice_generator'    => ['enabled' => true,  'label' => 'AI Invoice Generator'],
        'chatbot_support'      => ['enabled' => true,  'label' => 'AI Chatbot Support'],
        'document_ocr'         => ['enabled' => false, 'label' => 'Document OCR'],
        'workflow_automation'  => ['enabled' => true,  'label' => 'Workflow Automation'],
        'erp_analytics'        => ['enabled' => true,  'label' => 'ERP Analytics'],
        'predictive_analytics' => ['enabled' => true,  'label' => 'Predictive Analytics'],
        'recommendations'      => ['enabled' => true,  'label' => 'Recommendation Engine'],
        'whatsapp'             => ['enabled' => false, 'label' => 'WhatsApp Integration'],
    ],

];
