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
            ->label('انتشار مقاله')
            ->icon('heroicon-o-globe-alt')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('انتشار مقاله در وبسایت')
            ->modalDescription('آیا مطمئن هستید که می‌خواهید این مقاله را در وبسایت منتشر کنید؟')
            ->modalSubmitActionLabel('انتشار مقاله')
            ->form([
                Forms\Components\TextInput::make('slug')
                    ->label('نشانی URL (Slug)')
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
                    ->helperText('نشانی یکتای مقاله در آدرس وبسایت'),
                Forms\Components\Select::make('category_id')
                    ->label('دسته‌بندی')
                    ->options(Category::pluck('name', 'id'))
                    ->default(fn (BlogContentPipeline $record) => $record->category_id)
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('وضعیت انتشار')
                    ->options([
                        'published' => 'منتشر شده',
                        'draft' => 'پیش‌نویس',
                        'scheduled' => 'زمان‌بندی شده',
                    ])
                    ->default('published')
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('تاریخ انتشار')
                    ->default(now())
                    ->visible(fn (Forms\Get $get): bool => $get('status') === 'scheduled'),
                Forms\Components\Toggle::make('featured')
                    ->label('مقاله ویژه')
                    ->default(false),
                Forms\Components\Textarea::make('summary')
                    ->label('خلاصه مقاله')
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
                            
                            $message = 'مقاله با موفقیت به‌روزرسانی شد';
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
                        
                        $message = 'مقاله با موفقیت منتشر شد';
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
                        ->body('مقاله «' . $post->title . '» آماده مشاهده است')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('مشاهده مقاله')
                                ->url(route('app.blog.show', $post->slug))
                                ->openUrlInNewTab(),
                        ])
                        ->send();
                        
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('خطا در انتشار مقاله')
                        ->body('خطا در انتشار مقاله: ' . $e->getMessage())
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