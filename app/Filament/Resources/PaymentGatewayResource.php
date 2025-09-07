<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentGatewayResource\Pages;
use App\Models\PaymentGateway;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\PaymentGatewayManager;

class PaymentGatewayResource extends Resource
{
    protected static ?string $model = PaymentGateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'پرداخت';

    protected static ?string $navigationLabel = 'درگاه‌های پرداخت';

    protected static ?string $modelLabel = 'درگاه پرداخت';

    protected static ?string $pluralModelLabel = 'درگاه‌های پرداخت';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات اصلی')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('نام درگاه')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('مثال: آسان پرداخت'),

                                TextInput::make('slug')
                                    ->label('شناسه یکتا')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('مثال: asanpardakht')
                                    ->helperText('شناسه یکتا برای درگاه - فقط حروف انگلیسی و خط تیره'),

                                TextInput::make('driver')
                                    ->label('کلاس درایور')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('مثال: App\\Services\\PaymentGateways\\AsanpardakhtGateway')
                                    ->helperText('مسیر کامل کلاس درایور درگاه'),

                                Select::make('sort_order')
                                    ->label('ترتیب نمایش')
                                    ->options([
                                        1 => 'اول',
                                        2 => 'دوم',
                                        3 => 'سوم',
                                        4 => 'چهارم',
                                        5 => 'پنجم',
                                    ])
                                    ->default(1)
                                    ->required(),
                            ]),

                        Textarea::make('description')
                            ->label('توضیحات')
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('توضیحات مختصر درباره درگاه پرداخت'),
                    ])
                    ->collapsible(),

                Section::make('تنظیمات درگاه')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('فعال')
                                    ->default(true)
                                    ->helperText('درگاه در دسترس کاربران باشد'),

                                Toggle::make('is_default')
                                    ->label('پیش‌فرض')
                                    ->default(false)
                                    ->helperText('درگاه پیش‌فرض برای پرداخت‌ها'),

                                TextInput::make('min_amount')
                                    ->label('حداقل مبلغ (تومان)')
                                    ->numeric()
                                    ->default(1000)
                                    ->required()
                                    ->helperText('حداقل مبلغ قابل پرداخت از طریق این درگاه'),

                                TextInput::make('max_amount')
                                    ->label('حداکثر مبلغ (تومان)')
                                    ->numeric()
                                    ->default(500000000)
                                    ->helperText('حداکثر مبلغ قابل پرداخت از طریق این درگاه'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('fee_percentage')
                                    ->label('کارمزد درصدی')
                                    ->numeric()
                                    ->default(0)
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->helperText('درصد کارمزد درگاه (مثال: 1.5 برای 1.5%)'),

                                TextInput::make('fee_fixed')
                                    ->label('کارمزد ثابت (تومان)')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('کارمزد ثابت درگاه (مثال: 1000 تومان'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('ارزهای پشتیبانی شده')
                    ->schema([
                        Select::make('supported_currencies')
                            ->label('ارزهای پشتیبانی شده')
                            ->multiple()
                            ->options([
                                'IRT' => 'تومان ایران',
                                'USD' => 'دلار آمریکا',
                                'EUR' => 'یورو',
                            ])
                            ->default(['IRT'])
                            ->required()
                            ->helperText('ارزهایی که این درگاه از آن‌ها پشتیبانی می‌کند'),
                    ])
                    ->collapsible(),

                Section::make('تنظیمات امنیتی')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Dynamic configuration based on gateway type
                                TextInput::make('config.terminal_id')
                                    ->label('شناسه پایانه (Terminal ID)')
                                    ->helperText('برای درگاه سپهر: شناسه 8 رقمی پایانه')
                                    ->visible(fn ($get) => str_contains($get('driver') ?? '', 'Sepehr'))
                                    ->rules(['string', 'max:255']),

                                TextInput::make('config.merchant_id')
                                    ->label('شناسه پذیرنده')
                                    ->helperText('برای درگاه آسان پرداخت و سایر درگاه‌ها')
                                    ->visible(fn ($get) => !str_contains($get('driver') ?? '', 'Sepehr'))
                                    ->rules(['string', 'max:255']),

                                TextInput::make('config.username')
                                    ->label('نام کاربری')
                                    ->helperText('نام کاربری API درگاه')
                                    ->visible(fn ($get) => !str_contains($get('driver') ?? '', 'Sepehr'))
                                    ->rules(['string', 'max:255']),

                                TextInput::make('config.api_key')
                                    ->label('کلید API')
                                    ->helperText('برای درگاه جیبیت و سایر درگاه‌های مدرن')
                                    ->visible(fn ($get) => str_contains($get('driver') ?? '', 'Jibit'))
                                    ->rules(['string', 'max:255']),

                                TextInput::make('config.api_secret')
                                    ->label('کلید مخفی API')
                                    ->password()
                                    ->helperText('برای درگاه جیبیت و سایر درگاه‌های مدرن')
                                    ->visible(fn ($get) => str_contains($get('driver') ?? '', 'Jibit'))
                                    ->rules(['string', 'max:255']),

                                TextInput::make('config.password')
                                    ->label('رمز عبور')
                                    ->password()
                                    ->helperText('رمز عبور API درگاه')
                                    ->visible(fn ($get) => !str_contains($get('driver') ?? '', 'Sepehr') && !str_contains($get('driver') ?? '', 'Jibit'))
                                    ->rules(['string', 'max:255']),

                                Toggle::make('config.sandbox')
                                    ->label('حالت تست')
                                    ->default(true)
                                    ->helperText('برای تست درگاه فعال باشد'),

                                Toggle::make('config.get_method')
                                    ->label('استفاده از متد GET')
                                    ->default(false)
                                    ->helperText('برای درگاه سپهر: استفاده از GET برای callback')
                                    ->visible(fn ($get) => str_contains($get('driver') ?? '', 'Sepehr')),

                                Toggle::make('config.rollback_enabled')
                                    ->label('امکان بازپرداخت')
                                    ->default(false)
                                    ->helperText('برای درگاه سپهر: باید از طریق پشتیبانی فعال شود')
                                    ->visible(fn ($get) => str_contains($get('driver') ?? '', 'Sepehr')),
                            ]),

                        // Gateway-specific additional configs
                        KeyValue::make('config')
                            ->label('سایر تنظیمات')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->addActionLabel('افزودن تنظیم')
                            ->deleteActionLabel('حذف')
                            ->reorderable(false)
                            ->columnSpanFull()
                            ->helperText('سایر تنظیمات اختیاری درگاه پرداخت (مانند timeout، retry_attempts، api_version)'),
                    ])
                    ->collapsible(),

                Section::make('لوگو و تصاویر')
                    ->schema([
                        FileUpload::make('logo_url')
                            ->label('لوگوی درگاه')
                            ->image()
                            ->imageEditor()
                            ->directory('gateways/logos')
                            ->maxSize(2048)
                            ->helperText('لوگوی درگاه پرداخت (حداکثر 2MB)'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('لوگو')
                    ->circular()
                    ->size(40),

                TextColumn::make('name')
                    ->label('نام درگاه')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('شناسه')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('driver')
                    ->label('درایور')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->driver;
                    }),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_default')
                    ->label('پیش‌فرض')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('supported_currencies')
                    ->label('ارزها')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        return $state;
                    }),

                TextColumn::make('fee_percentage')
                    ->label('کارمزد')
                    ->formatStateUsing(function ($record) {
                        $fee = '';
                        if ($record->fee_percentage > 0) {
                            $fee .= $record->fee_percentage . '%';
                        }
                        if ($record->fee_fixed > 0) {
                            if ($fee) $fee .= ' + ';
                            $fee .= number_format($record->fee_fixed) . ' تومان';
                        }
                        return $fee ?: 'بدون کارمزد';
                    }),

                TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

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

                SelectFilter::make('is_default')
                    ->label('پیش‌فرض')
                    ->options([
                        '1' => 'پیش‌فرض',
                        '0' => 'غیر پیش‌فرض',
                    ]),

                SelectFilter::make('supported_currencies')
                    ->label('ارز')
                    ->options([
                        'IRT' => 'تومان ایران',
                        'USD' => 'دلار آمریکا',
                        'EUR' => 'یورو',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),

                Action::make('test_connection')
                    ->label('تست اتصال')
                    ->icon('heroicon-o-wifi')
                    ->color('info')
                    ->action(function (PaymentGateway $record) {
                        try {
                            $gatewayManager = app(PaymentGatewayManager::class);
                            $result = $gatewayManager->testGateway($record);
                            
                            if ($result['success']) {
                                Notification::make()
                                    ->title('اتصال موفق')
                                    ->body('درگاه پرداخت با موفقیت تست شد')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('خطا در اتصال')
                                    ->body('خطا: ' . ($result['error'] ?? 'خطای نامشخص'))
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطا در تست')
                                ->body('خطا: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('view_stats')
                    ->label('آمار')
                    ->icon('heroicon-o-chart-bar')
                    ->color('success')
                    ->action(function (PaymentGateway $record) {
                        try {
                            $gatewayManager = app(PaymentGatewayManager::class);
                            $stats = $gatewayManager->getGatewayStats($record);
                            
                            $message = "آمار درگاه {$record->name}:\n";
                            $message .= "کل تراکنش‌ها: " . $stats['total_transactions'] . "\n";
                            $message .= "تراکنش‌های موفق: " . $stats['successful_transactions'] . "\n";
                            $message .= "نرخ موفقیت: " . $stats['success_rate'] . "%\n";
                            $message .= "مجموع مبالغ: " . number_format($stats['total_amount']) . " تومان";
                            
                            Notification::make()
                                ->title('آمار درگاه')
                                ->body($message)
                                ->info()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطا در دریافت آمار')
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
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListPaymentGateways::route('/'),
            'create' => Pages\CreatePaymentGateway::route('/create'),
            'edit' => Pages\EditPaymentGateway::route('/{record}/edit'),
        ];
    }
} 