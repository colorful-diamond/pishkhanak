<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketStatusResource\Pages;
use App\Filament\Resources\TicketStatusResource\RelationManagers;
use App\Models\TicketStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketStatusResource extends Resource
{
    protected static ?string $model = TicketStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'وضعیت‌های تیکت';
    
    protected static ?string $modelLabel = 'وضعیت تیکت';
    
    protected static ?string $pluralModelLabel = 'وضعیت‌های تیکت';

    protected static ?string $navigationGroup = 'پشتیبانی';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات پایه')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام وضعیت')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('باز، بسته، در حال بررسی، ...'),
                        Forms\Components\TextInput::make('slug')
                            ->label('شناسه یکتا')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('open، closed، in-progress، ...'),
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->placeholder('توضیح مختصری از این وضعیت')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('نمایش و ظاهر')
                    ->schema([
                        Forms\Components\ColorPicker::make('color')
                            ->label('رنگ')
                            ->required()
                            ->default('#3B82F6'),
                        Forms\Components\TextInput::make('icon')
                            ->label('آیکن')
                            ->maxLength(255)
                            ->placeholder('heroicon-o-check-circle'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('تنظیمات')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->helperText('آیا این وضعیت فعال است؟')
                            ->required(),
                        Forms\Components\Toggle::make('is_default')
                            ->label('پیش‌فرض')
                            ->helperText('آیا این وضعیت پیش‌فرض باشد؟')
                            ->required(),
                        Forms\Components\Toggle::make('is_closed')
                            ->label('بسته محسوب شود')
                            ->helperText('آیا تیکت‌های با این وضعیت، بسته محسوب شوند؟')
                            ->required(),
                        Forms\Components\Toggle::make('is_resolved')
                            ->label('حل شده محسوب شود')
                            ->helperText('آیا تیکت‌های با این وضعیت، حل شده محسوب شوند؟')
                            ->required(),
                        Forms\Components\Toggle::make('requires_user_action')
                            ->label('نیاز به اقدام کاربر')
                            ->helperText('آیا این وضعیت نیاز به اقدام کاربر دارد؟')
                            ->required(),
                        Forms\Components\TextInput::make('auto_close_after')
                            ->label('بستن خودکار بعد از (ساعت)')
                            ->helperText('تیکت بعد از چند ساعت خودکار بسته شود؟')
                            ->numeric()
                            ->suffix('ساعت'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('next_status_options')
                            ->label('گزینه‌های وضعیت بعدی')
                            ->placeholder('open,closed'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام وضعیت')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('شناسه')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\ColorColumn::make('color')
                    ->label('رنگ'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('پیش‌فرض')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning'),
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('بسته')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_resolved')
                    ->label('حل شده')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('auto_close_after')
                    ->label('بستن خودکار (ساعت)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('ندارد'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت فعالیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('پیش‌فرض')
                    ->placeholder('همه')
                    ->trueLabel('پیش‌فرض')
                    ->falseLabel('عادی'),
                Tables\Filters\TernaryFilter::make('is_closed')
                    ->label('نوع بستن')
                    ->placeholder('همه')
                    ->trueLabel('بسته محسوب می‌شود')
                    ->falseLabel('باز محسوب می‌شود'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف انتخاب شده‌ها'),
                    
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
            ]);
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
            'index' => Pages\ListTicketStatuses::route('/'),
            'create' => Pages\CreateTicketStatus::route('/create'),
            'edit' => Pages\EditTicketStatus::route('/{record}/edit'),
        ];
    }
}
