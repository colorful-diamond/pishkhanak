<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Services\TemplateAutoResponseService;
use Illuminate\Console\Command;

class TestTemplateAutoResponse extends Command
{
    protected $signature = 'ticket:test-auto-response';
    protected $description = 'Test the template auto-response system with sample tickets';

    public function handle(TemplateAutoResponseService $service): void
    {
        $this->info('Testing template auto-response system...');

        // Get or create a test user
        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please create a user first.');
            return;
        }

        $testCases = [
            [
                'subject' => 'PERSIAN_TEXT_c6bb4bef',
                'description' => 'PERSIAN_TEXT_6e2f3bdd',
                'expected_template' => 'PERSIAN_TEXT_cbfb0703'
            ],
            [
                'subject' => 'PERSIAN_TEXT_57c981ee',
                'description' => 'PERSIAN_TEXT_9e1fbead',
                'expected_template' => 'PERSIAN_TEXT_0c1d3f4a'
            ],
            [
                'subject' => 'PERSIAN_TEXT_2f873527',
                'description' => 'PERSIAN_TEXT_0dfd3859',
                'expected_template' => 'PERSIAN_TEXT_0bfcc2a4'
            ],
            [
                'subject' => 'PERSIAN_TEXT_2a2a2e83',
                'description' => 'PERSIAN_TEXT_6854cb5a',
                'expected_template' => 'PERSIAN_TEXT_ebb672cb'
            ]
        ];

        foreach ($testCases as $index => $testCase) {
            $this->info("\n--- Test Case " . ($index + 1) . " ---");
            $this->info("Subject: " . $testCase['subject']);
            $this->info("Expected: " . $testCase['expected_template']);

            // Create test ticket
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'subject' => $testCase['subject'],
                'description' => $testCase['description'],
                'status' => 'open',
                'priority' => 'medium',
                'category' => 'general',
            ]);

            // Test auto-response
            $result = $service->processTicket($ticket);

            if ($result) {
                $this->info('✅ Auto-response applied: ' . $result['title']);
                
                // Check if it matches expected
                if ($result['title'] === $testCase['expected_template']) {
                    $this->info('✅ Correct template matched!');
                } else {
                    $this->warn('⚠️  Different template matched: ' . $result['title']);
                }
                
                // Show the ticket messages
                $ticket->refresh();
                $autoMessage = $ticket->messages()->latest()->first();
                if ($autoMessage) {
                    $this->info('Auto-response preview: ' . mb_substr(strip_tags($autoMessage->message), 0, 100) . '...');
                }
            } else {
                $this->warn('❌ No auto-response triggered');
            }

            // Clean up - delete test ticket
            $ticket->messages()->delete();
            $ticket->delete();
        }

        $this->info("\n✅ Testing completed!");
    }
}