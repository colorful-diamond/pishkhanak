<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Log;

class TemplateAutoResponseService
{
    /**
     * Process a ticket for template-based auto-response
     */
    public function processTicket(Ticket $ticket): ?array
    {
        if (!config('gemini.auto_response.enabled', true)) {
            return null;
        }

        try {
            $text = strtolower($ticket->subject . ' ' . $ticket->description);
            
            // Simple keyword matching for different contexts
            $matchedTemplate = $this->matchTemplate($text);
            
            if (!$matchedTemplate) {
                Log::info('No template matched for ticket', ['ticket_id' => $ticket->id]);
                return null;
            }

            // Apply the template response
            $success = $this->applyTemplateResponse($ticket, $matchedTemplate);
            
            if ($success) {
                Log::info('Template auto-response applied', [
                    'ticket_id' => $ticket->id,
                    'template_id' => $matchedTemplate['id'],
                    'template_title' => $matchedTemplate['title']
                ]);
                return $matchedTemplate;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error processing template auto-response', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Match ticket content to appropriate template
     */
    private function matchTemplate(string $text): ?array
    {
        $templates = $this->getResponseTemplates();
        
        // Define keywords for each template context
        $contextKeywords = [
            8 => ['PERSIAN_TEXT_0108e4e9', 'PERSIAN_TEXT_a201dd06', 'PERSIAN_TEXT_03d7e215', 'PERSIAN_TEXT_1215a837', 'PERSIAN_TEXT_7e8dbb98', 'PERSIAN_TEXT_83e336be'], // Loan inquiry
            9 => ['PERSIAN_TEXT_a7976da7', 'PERSIAN_TEXT_afd4d326', 'PERSIAN_TEXT_5031b178', 'PERSIAN_TEXT_90c9e7ca'], // Wallet refund policy
            11 => ['PERSIAN_TEXT_b0faa97d', 'PERSIAN_TEXT_a1c8a68b', 'PERSIAN_TEXT_7641f5da', 'PERSIAN_TEXT_5f169a53', 'PERSIAN_TEXT_7418f052', 'PERSIAN_TEXT_ed6ec08b'], // Credit score guide
            12 => ['PERSIAN_TEXT_b0faa97d', 'PERSIAN_TEXT_a1c8a68b', 'PERSIAN_TEXT_e73a8203', 'PERSIAN_TEXT_0e7108ad', 'PERSIAN_TEXT_7183510e', 'PERSIAN_TEXT_455d065e', 'PERSIAN_TEXT_21aaacba', 'PERSIAN_TEXT_261fe281'], // Credit score troubleshooting
            1 => ['PERSIAN_TEXT_78903c57', 'PERSIAN_TEXT_725c7ad5', 'PERSIAN_TEXT_ab64fafe', 'PERSIAN_TEXT_07dbb26d'], // Acknowledgment
            2 => ['PERSIAN_TEXT_312d5ad7', 'PERSIAN_TEXT_972f0d56', 'PERSIAN_TEXT_d65b37fd', 'PERSIAN_TEXT_b24373fe'], // More info request
        ];

        $bestMatch = null;
        $bestScore = 0;
        $minScore = 0.15; // 15% minimum match (lower threshold)

        foreach ($contextKeywords as $templateId => $keywords) {
            $matchCount = 0;
            $totalKeywords = count($keywords);
            
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $matchCount++;
                }
            }
            
            $score = $matchCount / $totalKeywords;
            
            // Add priority for credit score issues (template 12 over 11)
            if ($templateId == 12 && $score > 0) {
                $hasErrorKeywords = false;
                foreach (['PERSIAN_TEXT_e73a8203', 'PERSIAN_TEXT_0e7108ad', 'PERSIAN_TEXT_7183510e', 'PERSIAN_TEXT_455d065e', 'PERSIAN_TEXT_21aaacba', 'PERSIAN_TEXT_261fe281'] as $errorWord) {
                    if (strpos($text, $errorWord) !== false) {
                        $hasErrorKeywords = true;
                        break;
                    }
                }
                if ($hasErrorKeywords) {
                    $score += 0.3; // Boost score for error-related keywords
                }
            }
            
            if ($score > $bestScore && $score >= $minScore) {
                $bestScore = $score;
                $bestMatch = $templates->firstWhere('id', $templateId);
            }
        }

        return $bestMatch;
    }

    /**
     * Apply template response to ticket
     */
    private function applyTemplateResponse(Ticket $ticket, array $template): bool
    {
        try {
            // Format the template content with ticket variables
            $responseContent = $this->formatTemplateContent($template['content'], $ticket);

            // Create the response message
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => 1, // System user
                'message' => $responseContent,
                'is_internal' => false,
                'template_id' => $template['id'],
            ]);

            // Update ticket status
            $ticket->update([
                'is_auto_responded' => true,
                'auto_responded_at' => now(),
                'first_response_at' => $ticket->first_response_at ?: now(),
                'status' => 'waiting_for_user', // Keep ticket open for user response
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error applying template response', [
                'ticket_id' => $ticket->id,
                'template_id' => $template['id'],
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format template content with ticket variables
     */
    private function formatTemplateContent(string $content, Ticket $ticket): string
    {
        $variables = [
            'ticket_number' => $ticket->ticket_number,
            'user_name' => $ticket->user->name ?? 'PERSIAN_TEXT_ee8c069b',
            'created_date' => $ticket->created_at->format('Y/m/d'),
            'created_time' => $ticket->created_at->format('H:i'),
        ];

        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    /**
     * Get response templates (same as TicketManagement component)
     */
    private function getResponseTemplates()
    {
        return collect([
            [
                'id' => 1,
                'title' => 'PERSIAN_TEXT_6e11900c',
                'content' => 'PERSIAN_TEXT_8c6d868a'
            ],
            [
                'id' => 2,
                'title' => 'PERSIAN_TEXT_be932b37',
                'content' => 'PERSIAN_TEXT_5d3c34e5'
            ],
            [
                'id' => 8,
                'title' => 'PERSIAN_TEXT_cbfb0703',
                'content' => 'PERSIAN_TEXT_9aa09fd7'
            ],
            [
                'id' => 9,
                'title' => 'PERSIAN_TEXT_0c1d3f4a',
                'content' => 'PERSIAN_TEXT_b901ee2a'
            ],
            [
                'id' => 11,
                'title' => 'PERSIAN_TEXT_0bfcc2a4',
                'content' => 'PERSIAN_TEXT_d07179ab'
            ],
            [
                'id' => 12,
                'title' => 'PERSIAN_TEXT_ebb672cb',
                'content' => 'PERSIAN_TEXT_03e3507f'
            ],
        ]);
    }

    /**
     * Check if auto-response is enabled
     */
    public function isEnabled(): bool
    {
        return config('gemini.auto_response.enabled', true);
    }
}