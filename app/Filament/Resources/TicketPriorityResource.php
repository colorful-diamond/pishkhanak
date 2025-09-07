<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketPriorityResource\Pages;
use App\Filament\Resources\TicketPriorityResource\RelationManagers;
use App\Models\TicketPriority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketPriorityResource extends Resource
{
    protected static ?string $model = TicketPriority::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'اولویت‌های تیکت';
    
    protected static ?string $modelLabel = 'اولویت تیکت';
    
    protected static ?string $pluralModelLabel = 'اولویت‌های تیکت';

    protected static ?string $navigationGroup = 'پشتیبانی';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات پایه')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام اولویت')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('کم، متوسط، بالا، فوری، ...'),
                        Forms\Components\TextInput::make('slug')
                            ->label('شناسه یکتا')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('low، medium، high، urgent، ...'),
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->placeholder('توضیح مختصری از این اولویت')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('نمایش و ظاهر')
                    ->schema([
                        Forms\Components\ColorPicker::make('color')
                            ->label('رنگ')
                            ->required()
                            ->default('#10B981'),
                        Forms\Components\TextInput::make('icon')
                            ->label('آیکن')
                            ->maxLength(255)
                            ->placeholder('heroicon-o-flag'),
                        Forms\Components\TextInput::make('level')
                            ->label('سطح اولویت (1-10)')
                            ->helperText('عدد بالاتر = اولویت بیشتر')
                            ->required()
                            ->numeric()
                            ->min(1)
                            ->max(10)
                            ->default(5),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('تنظیمات')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->helperText('آیا این اولویت فعال است؟')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('ارتقاء خودکار')
                    ->schema([
                        Forms\Components\TextInput::make('auto_escalate_after')
                            ->label('ارتقاء خودکار بعد از (ساعت)')
                            ->helperText('بعد از چند ساعت اولویت خودکار ارتقاء یابد؟')
                            ->numeric()
                            ->suffix('ساعت'),
                        Forms\Components\Select::make('escalate_to_priority_id')
                            ->label('ارتقاء به اولویت')
                            ->relationship('escalateToPriority', 'name')
                            ->placeholder('انتخاب کنید'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام اولویت')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('level')
                    ->label('سطح')
                    ->numeric()
                    ->sortable()
                    ->colors([
                        'danger' => fn ($state) => $state >= 8,
                        'warning' => fn ($state) => $state >= 6 && $state < 8,
                        'success' => fn ($state) => $state >= 4 && $state < 6,
                        'gray' => fn ($state) => $state < 4,
                    ]),
                Tables\Columns\ColorColumn::make('color')
                    ->label('رنگ'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('شناسه')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('auto_escalate_after')
                    ->label('ارتقاء خودکار (ساعت)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('ندارد'),
                Tables\Columns\TextColumn::make('escalateToPriority.name')
                    ->label('ارتقاء به')
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
                Tables\Filters\SelectFilter::make('level')
                    ->label('سطح اولویت')
                    ->options([
                        1 => 'خیلی کم (1)',
                        2 => 'کم (2)',
                        3 => 'کم تا متوسط (3)',
                        4 => 'متوسط (4)',
                        5 => 'متوسط تا بالا (5)',
                        6 => 'بالا (6)',
                        7 => 'خیلی بالا (7)',
                        8 => 'فوری (8)',
                        9 => 'بحرانی (9)',
                        10 => 'اضطراری (10)',
                    ]),
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
            'index' => Pages\ListTicketPriorities::route('/'),
            'create' => Pages\CreateTicketPriority::route('/create'),
            'edit' => Pages\EditTicketPriority::route('/{record}/edit'),
        ];
    }
}
