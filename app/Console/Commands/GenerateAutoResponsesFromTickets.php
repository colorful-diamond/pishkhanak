<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\AutoResponseContext;
use App\Models\AutoResponse;
use App\Services\GeminiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAutoResponsesFromTickets extends Command
{
    protected $signature = 'tickets:generate-auto-responses {--limit=500} {--lang=fa}';

    protected $description = 'Analyze past tickets with Gemini and generate suggested auto-response contexts and responses';

    public function handle(GeminiService $gemini)
    {
        $limit = (int)$this->option('limit');
        $lang = (string)$this->option('lang');

        $this->info("Analyzing last {$limit} tickets to propose contexts and responses (lang={$lang})...");

        $tickets = Ticket::orderByDesc('created_at')->limit($limit)->get(['subject','description','category']);

        if ($tickets->isEmpty()) {
            $this->warn('No tickets found.');
            return Command::SUCCESS;
        }

        $corpus = $tickets->map(function ($t) {
            return [
                'subject' => (string)$t->subject,
                'description' => (string)$t->description,
                'category' => (string)$t->category,
            ];
        })->toJson(JSON_UNESCAPED_UNICODE);

        $instruction = 'You are an AI helping to create an FAQ-like auto-response system for a Persian/English support site. '
            . 'Given a JSON array of tickets (subject, description, category), cluster them into at most 8 practical contexts. '
            . 'For each context, return: name, description (Persian), keywords (comma-separated), example_queries (3-5 lines, Persian), '
            . 'and one suggested response template in Persian with placeholders like {{user_name}} and {{ticket_number}}. '
            . 'Output strict JSON: {"contexts": [{"name":"...","description":"...","keywords":"a,b,c","example_queries":"q1\nq2\nq3","response":"..."}]}';

        $messages = [
            ['role' => 'user', 'content' => "LANG={$lang}\nTICKETS=\n{$corpus}"],
        ];

        $json = $gemini->chatCompletionWithJsonValidation($messages, $instruction, 'gemini-2.5-flash', ['temperature' => 0.2]);

        $contexts = $json['contexts'] ?? [];
        if (!is_array($contexts) || empty($contexts)) {
            $this->error('No contexts produced by AI.');
            return Command::FAILURE;
        }

        DB::beginTransaction();
        try {
            foreach ($contexts as $c) {
                $name = (string)($c['name'] ?? '');
                if ($name === '') {
                    continue;
                }
                $context = AutoResponseContext::firstOrCreate(
                    ['name' => $name],
                    [
                        'description' => (string)($c['description'] ?? ''),
                        'keywords' => (string)($c['keywords'] ?? ''),
                        'example_queries' => (string)($c['example_queries'] ?? ''),
                        'is_active' => true,
                        'priority' => 10,
                        'confidence_threshold' => 0.7,
                    ]
                );

                $responseText = (string)($c['response'] ?? '');
                if ($responseText !== '') {
                    AutoResponse::firstOrCreate(
                        [
                            'context_id' => $context->id,
                            'title' => mb_substr($name, 0, 120),
                            'language' => $lang,
                        ],
                        [
                            'response_text' => $responseText,
                            'is_active' => true,
                            'mark_as_resolved' => false,
                            'usage_count' => 0,
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Failed to persist AI suggestions: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info('Auto-response contexts and responses generated/updated successfully.');
        return Command::SUCCESS;
    }
}


