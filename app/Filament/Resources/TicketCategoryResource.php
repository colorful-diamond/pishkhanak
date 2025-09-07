<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketCategoryResource\Pages;
use App\Models\TicketCategory;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TicketCategoryResource extends Resource
{
    protected static ?string $model = TicketCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'دسته‌بندی تیکت‌ها';
    
    protected static ?string $modelLabel = 'دسته‌بندی';
    
    protected static ?string $pluralModelLabel = 'دسته‌بندی‌ها';

    protected static ?string $navigationGroup = 'پشتیبانی';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات پایه')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام دسته‌بندی')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->label('نام انگلیسی (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z0-9-]+$/')
                            ->helperText('فقط حروف انگلیسی، اعداد و خط تیره مجاز است'),

                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->maxLength(1000)
                            ->rows(3),

                        Forms\Components\ColorPicker::make('color')
                            ->label('رنگ دسته‌بندی')
                            ->default('#3B82F6'),

                        Forms\Components\TextInput::make('icon')
                            ->label('آیکون (Heroicon)')
                            ->placeholder('heroicon-o-folder')
                            ->helperText('نام آیکون Heroicon (مثال: heroicon-o-folder)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('تنظیمات پیشرفته')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),

                        Forms\Components\Select::make('auto_assign_to')
                            ->label('اختصاص خودکار به')
                            ->options(User::role(['admin', 'support'])->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('بدون اختصاص خودکار'),

                        Forms\Components\TextInput::make('estimated_response_time')
                            ->label('زمان تخمینی پاسخ (دقیقه)')
                            ->numeric()
                            ->min(1)
                            ->placeholder('مثال: 120'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0),

                        Forms\Components\KeyValue::make('required_fields')
                            ->label('فیلدهای اجباری')
                            ->keyLabel('نام فیلد')
                            ->valueLabel('عنوان فیلد')
                            ->helperText('فیلدهایی که هنگام ایجاد تیکت در این دسته‌بندی اجباری هستند'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('رنگ'),

                Tables\Columns\TextColumn::make('name')
                    ->label('نام دسته‌بندی')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('description')
                    ->label('توضیحات')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),

                Tables\Columns\TextColumn::make('autoAssignUser.name')
                    ->label('اختصاص خودکار')
                    ->toggleable()
                    ->placeholder('ندارد'),

                Tables\Columns\TextColumn::make('estimated_response_time')
                    ->label('زمان تخمینی پاسخ')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' دقیقه' : 'تعریف نشده')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ticket_count')
                    ->label('تعداد تیکت‌ها')
                    ->counts('tickets')
                    ->sortable(),

                Tables\Columns\TextColumn::make('open_ticket_count')
                    ->label('تیکت‌های باز')
                    ->getStateUsing(fn (TicketCategory $record) => $record->open_ticket_count)
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),

                Tables\Filters\SelectFilter::make('auto_assign_to')
                    ->label('اختصاص خودکار')
                    ->options(User::role(['admin', 'support'])->pluck('name', 'id'))
                    ->placeholder('همه'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('فعال کردن')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        }),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('غیرفعال کردن')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        }),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketCategories::route('/'),
            'create' => Pages\CreateTicketCategory::route('/create'),
            'view' => Pages\ViewTicketCategory::route('/{record}'),
            'edit' => Pages\EditTicketCategory::route('/{record}/edit'),
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