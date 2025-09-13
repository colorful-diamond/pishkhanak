<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPipelineResource\Pages;
use App\Models\BlogContentPipeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BlogPipelineResource\Actions\GenerateAiContentEnhancedAction;
use App\Filament\Resources\BlogPipelineResource\Actions\PublishPostAction;
use App\Filament\Resources\BlogPipelineResource\Actions\BulkGenerateContentAction;

class BlogPipelineResource extends Resource
{
    protected static ?string $model = BlogContentPipeline::class;

    protected static ?string $navigationGroup = 'هوش مصنوعی';
    
    protected static ?int $navigationSort = 8;
    
    protected static ?string $navigationLabel = 'خط تولید محتوا';
    
    protected static ?string $pluralLabel = 'فرایندهای تولید محتوا';
    
    protected static ?string $label = 'فرایند تولید محتوا';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(500),
                Forms\Components\Select::make('status')
                    ->options([
                        'imported' => 'Imported',
                        'queued' => 'Queued',
                        'processing' => 'Processing',
                        'processed' => 'Processed',
                        'reviewed' => 'Reviewed',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                        'failed' => 'Failed',
                        'skipped' => 'Skipped',
                    ])
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('original_content')
                    ->label('Content')
                    ->rows(10)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('title')
                    ->label('Title')
                    ->limit(50)
                    ->searchable(),
                    
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'imported' => 'gray',
                        'queued' => 'warning',
                        'processing' => 'primary',
                        'processed' => 'success',
                        'reviewed' => 'info',
                        'scheduled' => 'success',
                        'published' => 'success',
                        'failed' => 'danger',
                        'skipped' => 'gray',
                        default => 'gray',
                    }),
                    
                TextColumn::make('quality_score')
                    ->label('Quality')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state * 100, 0) . '%' : '-')
                    ->badge()
                    ->sortable(),
                    
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable(),
                    
                TextColumn::make('created_at')
                    ->label('Imported')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'imported' => 'Imported',
                        'queued' => 'Queued',
                        'processing' => 'Processing',
                        'processed' => 'Processed',
                        'reviewed' => 'Reviewed',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                        'failed' => 'Failed',
                        'skipped' => 'Skipped',
                    ])
                    ->multiple(),
                    
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                    
                Filter::make('requires_review')
                    ->query(fn (Builder $query): Builder => $query->where('requires_review', true))
                    ->label('Requires Review'),
            ])
            ->actions([
                GenerateAiContentEnhancedAction::make(),
                Tables\Actions\Action::make('monitor_processing')
                    ->label('مونیتورینگ پردازش')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'processing')
                    ->url(function ($record) {
                        $status = \App\Models\AiProcessingStatus::where('pipeline_id', $record->id)
                            ->latest()
                            ->first();
                        if ($status) {
                            return route('filament.access.resources.ai-processing-statuses.view', ['record' => $status->id]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab(),
                PublishPostAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkGenerateContentAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPipelines::route('/'),
            'create' => Pages\CreateBlogPipeline::route('/create'),
            'edit' => Pages\EditBlogPipeline::route('/{record}/edit'),
        ];
    }
}