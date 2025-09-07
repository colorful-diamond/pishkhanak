<?php

namespace App\Filament\Resources\BlogPipelineResource\Actions;

use App\Models\BlogContentPipeline;
use App\Models\Post;
use App\Models\Category;
use App\Services\GeminiService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Str;

class PublishPostAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'publish')
            ->label('PERSIAN_TEXT_f5b3e7eb')
            ->icon('heroicon-o-globe-alt')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('PERSIAN_TEXT_c66b50b0')
            ->modalDescription('PERSIAN_TEXT_1a80dcf3')
            ->modalSubmitActionLabel('PERSIAN_TEXT_68e323cd')
            ->form([
                Forms\Components\TextInput::make('slug')
                    ->label('PERSIAN_TEXT_7c76fed6')
                    ->required()
                    ->default(function (BlogContentPipeline $record) {
                        try {
                            $geminiService = app(GeminiService::class);
                            $title = $record->ai_title ?: $record->title;
                            
                            $prompt = "Generate a short, SEO-friendly English slug for this Persian blog post title. 
                            Title: {$title}
                            
                            Requirements:
                            - Translate to English if needed
                            - Maximum 60 characters
                            - Use hyphens between words
                            - All lowercase
                            - No special characters
                            - Should be descriptive and SEO-friendly
                            
                            Return ONLY the slug, nothing else.";
                            
                            $slug = $geminiService->generateContent($prompt, 'gemini-2.5-flash');
                            return Str::slug($slug);
                        } catch (\Exception $e) {
                            // Fallback to basic slug generation
                            return Str::slug($record->ai_title ?: $record->title);
                        }
                    })
                    ->helperText('PERSIAN_TEXT_04c9f639'),
                Forms\Components\Select::make('category_id')
                    ->label('PERSIAN_TEXT_b561a47a')
                    ->options(Category::pluck('name', 'id'))
                    ->default(fn (BlogContentPipeline $record) => $record->category_id)
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('PERSIAN_TEXT_fe00462b')
                    ->options([
                        'published' => 'PERSIAN_TEXT_2d5566e3',
                        'draft' => 'PERSIAN_TEXT_6ed250fe',
                        'scheduled' => 'PERSIAN_TEXT_49a77064',
                    ])
                    ->default('published')
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('PERSIAN_TEXT_394a9f99')
                    ->default(now())
                    ->visible(fn (Forms\Get $get): bool => $get('status') === 'scheduled'),
                Forms\Components\Toggle::make('featured')
                    ->label('PERSIAN_TEXT_253ec389')
                    ->default(false),
                Forms\Components\Textarea::make('summary')
                    ->label('PERSIAN_TEXT_f5d8f72d')
                    ->default(fn (BlogContentPipeline $record) => $record->ai_summary ?: $record->original_summary)
                    ->rows(3),
            ])
            ->action(function (BlogContentPipeline $record, array $data): void {
                try {
                    // Check if post already exists
                    if ($record->published_post_id) {
                        $post = Post::find($record->published_post_id);
                        if ($post) {
                            // Update existing post
                            $post->update([
                                'title' => $record->ai_title ?: $record->title,
                                'slug' => $data['slug'],
                                'content' => $record->ai_content ?: $record->original_content,
                                'summary' => $data['summary'] ?? ($record->ai_summary ?: $record->original_summary),
                                'category_id' => $data['category_id'],
                                'status' => $data['status'],
                                'featured' => $data['featured'] ?? false,
                                'published_at' => $data['status'] === 'scheduled' ? $data['published_at'] : now(),
                                'meta_title' => $record->meta_title,
                                'meta_description' => $record->meta_description,
                                'meta_keywords' => is_array($record->meta_keywords) ? implode(',', $record->meta_keywords) : $record->meta_keywords,
                            ]);
                            
                            $message = 'PERSIAN_TEXT_babac2e2';
                        }
                    } else {
                        // Create new post
                        $post = Post::create([
                            'title' => $record->ai_title ?: $record->title,
                            'slug' => $data['slug'],
                            'content' => $record->ai_content ?: $record->original_content,
                            'summary' => $data['summary'] ?? ($record->ai_summary ?: $record->original_summary),
                            'category_id' => $data['category_id'],
                            'author_id' => auth()->id(),  // Changed from user_id to author_id
                            'status' => $data['status'],
                            'featured' => $data['featured'] ?? false,
                            'published_at' => $data['status'] === 'scheduled' ? $data['published_at'] : now(),
                            'meta_title' => $record->meta_title,
                            'meta_description' => $record->meta_description,
                            'meta_keywords' => is_array($record->meta_keywords) ? implode(',', $record->meta_keywords) : $record->meta_keywords,
                            'views' => 0,
                        ]);
                        
                        $message = 'PERSIAN_TEXT_38121f62';
                    }
                    
                    // Update pipeline record
                    $record->update([
                        'status' => BlogContentPipeline::STATUS_PUBLISHED,
                        'published_at' => now(),
                        'published_post_id' => $post->id,
                    ]);
                    
                    // FAQ can be added here if needed in future
                    
                    Notification::make()
                        ->success()
                        ->title($message)
                        ->body('PERSIAN_TEXT_eb5ccb48' . $post->title)
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('PERSIAN_TEXT_1722ad5e')
                                ->url(route('app.blog.show', $post->slug))
                                ->openUrlInNewTab(),
                        ])
                        ->send();
                        
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('PERSIAN_TEXT_1c126031')
                        ->body('PERSIAN_TEXT_05f6ce76' . $e->getMessage())
                        ->send();
                }
            })
            ->visible(fn (BlogContentPipeline $record): bool => 
                in_array($record->status, [
                    BlogContentPipeline::STATUS_PROCESSED,
                    BlogContentPipeline::STATUS_REVIEWED,
                    BlogContentPipeline::STATUS_PUBLISHED,
                ])
            );
    }
}