<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'پرداخت';

    protected static ?string $navigationLabel = 'ارزها';

    protected static ?string $modelLabel = 'ارز';

    protected static ?string $pluralModelLabel = 'ارزها';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات اصلی')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->label('کد ارز')
                                    ->required()
                                    ->maxLength(3)
                                    ->unique(ignoreRecord: true)
                                                    ->placeholder('مثال: IRT')
                ->helperText('کد سه حرفی ارز (مثال: IRT, USD, EUR)'),

                                TextInput::make('name')
                                    ->label('نام ارز')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('مثال: تومان ایران'),

                                TextInput::make('symbol')
                                    ->label('نماد ارز')
                                    ->required()
                                    ->maxLength(10)
                                    ->placeholder('مثال: ﷼'),

                                TextInput::make('exchange_rate')
                                    ->label('نرخ تبدیل')
                                    ->numeric()
                                    ->default(1.0000)
                                    ->step(0.0001)
                                    ->required()
                                    ->helperText('نرخ تبدیل نسبت به ارز پایه'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('تنظیمات نمایش')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('فعال')
                                    ->default(true)
                                    ->helperText('ارز در دسترس کاربران باشد'),

                                Toggle::make('is_base_currency')
                                    ->label('ارز پایه')
                                    ->default(false)
                                    ->helperText('ارز پایه سیستم (فقط یک ارز می‌تواند پایه باشد)'),

                                TextInput::make('decimal_places')
                                    ->label('تعداد اعشار')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(8)
                                    ->required()
                                    ->helperText('تعداد ارقام اعشار (مثال: 0 برای تومان، 2 برای دلار)'),

                                Select::make('position')
                                    ->label('موقعیت نماد')
                                    ->options([
                                        'before' => 'قبل از عدد',
                                        'after' => 'بعد از عدد',
                                    ])
                                    ->default('after')
                                    ->required()
                                    ->helperText('موقعیت نماد ارز نسبت به عدد'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('کد')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('symbol')
                    ->label('نماد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('exchange_rate')
                    ->label('نرخ تبدیل')
                    ->numeric(
                        decimalPlaces: 4,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_base_currency')
                    ->label('ارز پایه')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('decimal_places')
                    ->label('اعشار')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('position')
                    ->label('موقعیت')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'before' => 'قبل',
                            'after' => 'بعد',
                            default => $state,
                        };
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('وضعیت')
                    ->options([
                        '1' => 'فعال',
                        '0' => 'غیرفعال',
                    ]),

                SelectFilter::make('is_base_currency')
                    ->label('ارز پایه')
                    ->options([
                        '1' => 'ارز پایه',
                        '0' => 'غیر پایه',
                    ]),

                SelectFilter::make('position')
                    ->label('موقعیت نماد')
                    ->options([
                        'before' => 'قبل از عدد',
                        'after' => 'بعد از عدد',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),

                Action::make('update_exchange_rate')
                    ->label('بروزرسانی نرخ')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Currency $record) {
                        try {
                            // This would typically call an external API
                            // For now, we'll just show a notification
                            Notification::make()
                                ->title('بروزرسانی نرخ')
                                ->body('نرخ تبدیل بروزرسانی شد')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطا در بروزرسانی')
                                ->body('خطا: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف انتخاب شده‌ها'),
                ]),
            ])
            ->defaultSort('code', 'asc');
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
} 