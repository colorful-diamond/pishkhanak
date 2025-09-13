<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutoResponseResource\Pages;
use App\Models\AutoResponse;
use App\Models\AutoResponseContext;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasResourcePermissions;

class AutoResponseResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = AutoResponse::class;

    protected static ?string $navigationGroup = 'مدیریت پشتیبانی';

    protected static ?string $navigationLabel = 'پاسخ‌های خودکار';

    protected static ?string $modelLabel = 'پاسخ خودکار';

    protected static ?string $pluralModelLabel = 'پاسخ‌های خودکار';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Forms\Components\Select::make('context_id')
                            ->label('زمینه')
                            ->relationship('context', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('زمینه یا موقعیتی که این پاسخ در آن استفاده می‌شود'),

                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255)
                            ->helperText('عنوان کوتاه و مفید برای شناسایی پاسخ'),

                        Forms\Components\Select::make('language')
                            ->label('زبان')
                            ->options([
                                'fa' => 'فارسی',
                                'en' => 'انگلیسی',
                            ])
                            ->default('fa')
                            ->required()
                            ->helperText('زبان پاسخ را انتخاب کنید'),

                        Forms\Components\RichEditor::make('response_text')
                            ->label('متن پاسخ')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('متن کامل پاسخی که به کاربر ارسال می‌شود'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('پیوست‌ها و لینک‌ها')
                    ->schema([
                        Forms\Components\Repeater::make('attachments')
                            ->label('پیوست‌ها')
                            ->schema([
                                Forms\Components\FileUpload::make('file_path')
                                    ->label('فایل')
                                    ->directory('auto-responses')
                                    ->required(),
                                Forms\Components\TextInput::make('description')
                                    ->label('توضیحات')
                                    ->maxLength(255),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['description'] ??'پیوست جدید')
                            ->addActionLabel('افزودن پیوست'),

                        Forms\Components\Repeater::make('links')
                            ->label('لینک‌ها')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('عنوان لینک')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url')
                                    ->label('آدرس URL')
                                    ->url()
                                    ->required()
                                    ->maxLength(500),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ??'لینک جدید')
                            ->addActionLabel('افزودن لینک'),
                    ]),

                Forms\Components\Section::make('تنظیمات')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->helperText('پاسخ فعال باشد تا استفاده شود'),

                        Forms\Components\Toggle::make('mark_as_resolved')
                            ->label('علامت‌گذاری به عنوان حل‌شده')
                            ->default(false)
                            ->helperText('تیکت به صورت خودکار حل‌شده علامت‌گذاری شود'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('آمار و تحلیل')
                    ->schema([
                        Forms\Components\Placeholder::make('usage_count')
                            ->label('تعداد استفاده')
                            ->content(fn ($record) => $record?->usage_count ?? 0),

                        Forms\Components\Placeholder::make('satisfaction_score')
                            ->label('امتیاز رضایت')
                            ->content(fn ($record) => $record?->satisfaction_score 
                                ? number_format($record->satisfaction_score, 1) . ' از ۵' 
                                : 'بدون داده'),

                        Forms\Components\Placeholder::make('effectiveness_percentage')
                            ->label('درصد اثربخشی')
                            ->content(fn ($record) => $record?->effectiveness_percentage !== null
                                ? $record->effectiveness_percentage . '%'
                                : 'بدون داده'),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('تاریخ ایجاد')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '-'),
                    ])
                    ->columns(4)
                    ->hidden(fn ($operation) => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('context.name')
                    ->label('زمینه')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('language')
                    ->label('زبان')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'fa' => 'فارسی',
                        'en' => 'English',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('mark_as_resolved')
                    ->label('علامت‌گذاری حل‌شده')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->tooltip(fn ($state) => $state 
                        ? 'تیکت به صورت خودکار حل‌شده علامت‌گذاری می‌شود'
                        : 'تیکت به صورت خودکار حل‌شده علامت‌گذاری نمی‌شود'),

                Tables\Columns\TextColumn::make('usage_count')
                    ->label('تعداد استفاده')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('satisfaction_score')
                    ->label('امتیاز رضایت')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state !== null 
                        ? number_format($state, 1) 
                        : '-')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        $state !== null => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('effectiveness_percentage')
                    ->label('درصد اثربخشی')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '%' : '-')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state !== null => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('context_id')
                    ->label('زمینه')
                    ->relationship('context', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('language')
                    ->label('زبان')
                    ->options([
                        'fa' => 'فارسی',
                        'en' => 'انگلیسی',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('فیلتر فعال بودن')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),

                Tables\Filters\TernaryFilter::make('mark_as_resolved')
                    ->label('فیلتر حل‌شده')
                    ->placeholder('همه')
                    ->trueLabel('حل‌شده')
                    ->falseLabel('حل‌نشده'),

                Tables\Filters\Filter::make('high_usage')
                    ->label('استفاده بالا')
                    ->query(fn (Builder $query) => $query->where('usage_count', '>=', 10)),

                Tables\Filters\Filter::make('low_satisfaction')
                    ->label('رضایت پایین')
                    ->query(fn (Builder $query) => $query->where('satisfaction_score', '<', 3)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('پیش‌نمایش')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('پیش‌نمایش پاسخ خودکار')
                    ->modalContent(fn ($record) => view('filament.resources.auto-response.preview', ['response' => $record]))
                    ->modalSubmitAction(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('فعال‌سازی')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('غیرفعال‌سازی')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
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
            'index' => Pages\ListAutoResponses::route('/'),
            'create' => Pages\CreateAutoResponse::route('/create'),
            'view' => Pages\ViewAutoResponse::route('/{record}'),
            'edit' => Pages\EditAutoResponse::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If context filter is set in the URL, apply it
        if (request()->has('context')) {
            $query->where('context_id', request('context'));
        }

        return $query;
    }
}
