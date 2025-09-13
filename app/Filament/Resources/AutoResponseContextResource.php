<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutoResponseContextResource\Pages;
use App\Models\AutoResponseContext;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasResourcePermissions;

class AutoResponseContextResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = AutoResponseContext::class;

    protected static ?string $navigationGroup = 'مدیریت تکت';

    protected static ?string $navigationLabel = 'زمینه‌های پاسخ خودکار';

    protected static ?string $modelLabel = 'زمینه پاسخ خودکار';

    protected static ?string $pluralModelLabel = 'زمینه‌های پاسخ خودکار';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('مشخصات پایه')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام زمینه')
                            ->required()
                            ->maxLength(255)
                            ->helperText('نام یکتا برای شناسایی زمینه'),

                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('توضیح کامل برای کاربرد زمینه'),

                        Forms\Components\Textarea::make('keywords')
                            ->label('کلیدواژه‌ها')
                            ->rows(2)
                            ->helperText('کلیدواژه‌های مربوط به این زمینه جهت تشخیص بهتر')
                            ->placeholder('مثل: پرداخت، تراکنش، پول، کیف پول'),

                        Forms\Components\Textarea::make('example_queries')
                            ->label('نمونه پرسش‌ها')
                            ->rows(4)
                            ->helperText('نمونه‌هایی از سوالاتی که کاربران ممکن است بپرسند')
                            ->placeholder("برای پرداخت چه کار کنم؟\nکیف پولم کجاست؟\nچگونه پول شارژ کنم؟"),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('تنظیمات پیشرفته')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال بودن')
                            ->default(true)
                            ->helperText('آیا این زمینه برای تشخیص پاسخ فعال باشد'),

                        Forms\Components\TextInput::make('priority')
                            ->label('اولویت')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(999)
                            ->helperText('اولویت زمینه (عدد بالاتر = اولویت بیشتر)'),

                        Forms\Components\TextInput::make('confidence_threshold')
                            ->label('حد اطمینان')
                            ->numeric()
                            ->default(0.7)
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.1)
                            ->helperText('حداقل درجه اطمینان نیاز برای فعال‌سازی زمینه'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('آمار و عملکرد')
                    ->schema([
                        Forms\Components\Placeholder::make('effectiveness_score')
                            ->label('نمره کارآمدی')
                            ->content(fn ($record) => $record?->effectiveness_score !== null 
                                ? $record->effectiveness_score . '%' 
                                : 'در دسترس نیست'),

                        Forms\Components\Placeholder::make('total_uses')
                            ->label('تعداد استفاده')
                            ->content(fn ($record) => $record?->logs()->count() ?? 0),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('تاریخ ایجاد')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '-'),
                    ])
                    ->columns(3)
                    ->hidden(fn ($operation) => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام زمینه')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('توضیحات')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('responses_count')
                    ->label('تعداد پاسخ‌ها')
                    ->counts('responses')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('priority')
                    ->label('اولویت')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'danger',
                        $state >= 50 => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('confidence_threshold')
                    ->label('حد اطمینان')
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 0) . '%')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('logs_count')
                    ->label('استفاده‌ها')
                    ->counts('logs')
                    ->badge(),

                Tables\Columns\TextColumn::make('effectiveness_score')
                    ->label('کارآمدی')
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
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه زمینه‌ها')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),

                Tables\Filters\Filter::make('has_responses')
                    ->label('دارای پاسخ')
                    ->query(fn (Builder $query) => $query->has('responses')),

                Tables\Filters\Filter::make('high_priority')
                    ->label('اولویت بالا')
                    ->query(fn (Builder $query) => $query->where('priority', '>=', 50)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manage_responses')
                    ->label('مدیریت پاسخ‌ها')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn ($record) => AutoResponseResource::getUrl('index', ['context' => $record->id]))
                    ->color('success'),
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
            ->defaultSort('priority', 'desc');
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
            'index' => Pages\ListAutoResponseContexts::route('/'),
            'create' => Pages\CreateAutoResponseContext::route('/create'),
            'view' => Pages\ViewAutoResponseContext::route('/{record}'),
            'edit' => Pages\EditAutoResponseContext::route('/{record}/edit'),
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
}
