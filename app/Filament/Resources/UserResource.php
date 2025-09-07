<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;

class UserResource extends Resource
{

    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static $enablePasswordUpdates = true;

    protected static $extendFormCallback = null;


    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت کاربران';
    }

    public static function getNavigationLabel(): string
    {
        return 'کاربران';
    }

    public static function getLabel(): string
    {
        return 'کاربر';
    }

    public static function getPluralLabel(): string
    {
        return 'کاربران';
    }

    public static function extendForm($callback): void
    {
        static::$extendFormCallback = $callback;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(function () {
                $schema = [
                    'left' => Card::make([
                        
                        'name' => TextInput::make('name')
                            ->label(__('filament-panels::resources/user.fields.name'))
                            ->required(),
                        'email' => TextInput::make('email')
                            ->label(__('filament-panels::resources/user.fields.email'))
                            ->required()
                            ->unique(ignoreRecord: true),
                        'mobile' => TextInput::make('mobile')
                            ->label('شماره موبایل')
                            ->tel()
                            ->maxLength(15)
                            ->unique(ignoreRecord: true),
                        'password' => TextInput::make('password')
                            ->label(__('filament-panels::resources/user.fields.password'))
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rule(Password::default()),
                        'new_password_group' => Group::make([
                            'new_password' => TextInput::make('new_password')
                                ->password()
                                ->label(__('filament-panels::resources/user.fields.new_password'))
                                ->nullable()
                                ->rule(Password::default())
                                ->dehydrated(false),
                            'new_password_confirmation' => TextInput::make('new_password_confirmation')
                                ->password()
                                ->label(__('filament-panels::resources/user.fields.new_password_confirmation'))
                                ->rule('required', fn ($get) => !! $get('new_password'))
                                ->same('new_password')
                                ->dehydrated(false),
                        ])->visible(fn (string $context): bool => $context === 'edit' && static::$enablePasswordUpdates)
                    ])->columnSpan(8),
                    'right' => Card::make([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->options([
                                'admin' => 'Admin',
                                'customer' => 'Customer', 
                                'partner' => 'Partner',
                            ])
                            ->default('customer'),
                        'created_at' => Placeholder::make('created_at')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                    ])->columnSpan(4),
                ];

                if (static::$extendFormCallback !== null) {
                    $schema = value(static::$extendFormCallback, $schema);
                }

                return $schema;
            })
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-panels::resources/user.fields.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament-panels::resources/user.fields.email'))
                    ->searchable(),
                TextColumn::make('mobile')
                    ->label('شماره موبایل')
                    ->searchable()
                    ->copyable()
                    ->placeholder('ندارد'),
                TextColumn::make('balance')
                    ->label('موجودی کیف پول')
                    ->formatStateUsing(fn (User $record): string => number_format($record->balance) . ' تومان')
                    ->badge()
                    ->color('success')
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                        'partner' => 'Partner',
                        default => $state
                    })
                    ->color(fn (string $state): string => match($state) {
                        'admin' => 'danger',
                        'customer' => 'gray',
                        'partner' => 'success',
                        default => 'gray'
                    }),
                TextColumn::make('created_at')
                    ->label(__('filament-panels::resources/user.fields.created_at'))
                    ->date('d M Y')
                    ->jalaliDate()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('charge_wallet')
                    ->label('شارژ کیف پول')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('مبلغ (تومان)')
                            ->required()
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue(50000000)
                            ->suffix('تومان')
                            ->placeholder('مثال: 10000'),
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->required()
                            ->placeholder('دلیل شارژ کیف پول را وارد کنید...')
                            ->maxLength(500),
                        Forms\Components\Select::make('reason_type')
                            ->label('نوع عملیات')
                            ->options([
                                'admin_charge' => 'شارژ توسط ادمین',
                                'support_refund' => 'بازگشت وجه پشتیبانی',
                                'promotional_credit' => 'اعتبار تبلیغاتی',
                                'error_correction' => 'تصحیح خطا',
                                'bonus' => 'جایزه و پاداش',
                                'other' => 'سایر موارد'
                            ])
                            ->required()
                            ->default('admin_charge')
                    ])
                    ->action(function (User $record, array $data): void {
                        try {
                            // Use Bavix wallet to deposit amount
                            $record->deposit($data['amount'], [
                                'description' => $data['description'],
                                'reason_type' => $data['reason_type'],
                                'admin_user_id' => Auth::id(),
                                'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                                'type' => 'admin_charge',
                                'payment_source' => 'admin_panel',
                                'payment_method' => 'admin_action',
                                'performed_at' => now()->toISOString(),
                                'source_tracking' => [
                                    'source_type' => 'admin_action',
                                    'source_id' => Auth::id(),
                                    'source_title' => 'شارژ توسط ادمین',
                                    'source_category' => 'Admin Management',
                                    'payment_flow' => 'admin_to_wallet',
                                    'user_type' => 'admin',
                                    'transaction_context' => 'admin_wallet_charge',
                                    'admin_user_id' => Auth::id(),
                                    'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                                    'reason_type' => $data['reason_type'],
                                    'performed_at' => now()->toISOString()
                                ]
                            ]);

                            // Log the action
                            \Illuminate\Support\Facades\Log::info('Admin wallet charge', [
                                'target_user_id' => $record->id,
                                'target_user_name' => $record->name,
                                'amount' => $data['amount'],
                                'description' => $data['description'],
                                'reason_type' => $data['reason_type'],
                                'admin_user_id' => Auth::id(),
                                'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('شارژ کیف پول موفق')
                                ->body("مبلغ " . number_format($data['amount']) . " تومان به کیف پول {$record->name} اضافه شد.")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('خطا در شارژ کیف پول')
                                ->body('متاسفانه خطایی رخ داد: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('deduct_wallet')
                    ->label('کسر از کیف پول')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('مبلغ (تومان)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('تومان')
                            ->placeholder('مثال: 5000')
                            ->helperText(fn (User $record) => 'موجودی فعلی: ' . number_format($record->balance) . ' تومان'),
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->required()
                            ->placeholder('دلیل کسر از کیف پول را وارد کنید...')
                            ->maxLength(500),
                        Forms\Components\Select::make('reason_type')
                            ->label('نوع عملیات')
                            ->options([
                                'admin_deduction' => 'کسر توسط ادمین',
                                'penalty' => 'جریمه',
                                'error_correction' => 'تصحیح خطا',
                                'chargeback' => 'برگشت تراکنش',
                                'fraud_prevention' => 'پیشگیری از تقلب',
                                'other' => 'سایر موارد'
                            ])
                            ->required()
                            ->default('admin_deduction'),
                        Forms\Components\Toggle::make('force_deduction')
                            ->label('اجباری (حتی در صورت موجودی ناکافی)')
                            ->helperText('در صورت فعال بودن، حتی اگر موجودی کافی نباشد، مبلغ کسر می‌شود')
                            ->default(false)
                    ])
                    ->action(function (User $record, array $data): void {
                        try {
                            // Check if user has sufficient balance unless force deduction is enabled
                            if (!$data['force_deduction'] && $record->balance < $data['amount']) {
                                \Filament\Notifications\Notification::make()
                                    ->title('موجودی ناکافی')
                                    ->body("موجودی کیف پول ({$record->balance} تومان) کمتر از مبلغ درخواستی ({$data['amount']} تومان) است.")
                                    ->warning()
                                    ->send();
                                return;
                            }

                            // Use appropriate withdrawal method
                            if ($data['force_deduction']) {
                                // Force withdraw even with insufficient balance
                                $record->forceWithdraw($data['amount'], [
                                    'description' => $data['description'],
                                    'reason_type' => $data['reason_type'],
                                    'admin_user_id' => Auth::id(),
                                    'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                                    'type' => 'admin_deduction',
                                    'force_deduction' => true,
                                    'payment_source' => 'admin_panel',
                                    'payment_method' => 'admin_action',
                                    'performed_at' => now()->toISOString(),
                                    'source_tracking' => [
                                        'source_type' => 'admin_action',
                                        'source_id' => Auth::id(),
                                        'source_title' => 'کسر اجباری توسط ادمین',
                                        'source_category' => 'Admin Management',
                                        'payment_flow' => 'wallet_to_admin_forced',
                                        'user_type' => 'admin',
                                        'transaction_context' => 'admin_wallet_force_deduction',
                                        'admin_user_id' => Auth::id(),
                                        'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                                        'reason_type' => $data['reason_type'],
                                        'force_deduction' => true,
                                        'performed_at' => now()->toISOString()
                                    ]
                                ]);
                            } else {
                                // Normal withdraw
                                $record->withdraw($data['amount'], [
                                    'description' => $data['description'],
                                    'reason_type' => $data['reason_type'],
                                    'admin_user_id' => Auth::id(),
                                    'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                                    'type' => 'admin_deduction',
                                    'force_deduction' => false,
                                    'payment_source' => 'admin_panel',
                                    'payment_method' => 'admin_action',
                                    'performed_at' => now()->toISOString(),
                                    'source_tracking' => [
                                        'source_type' => 'admin_action',
                                        'source_id' => Auth::id(),
                                        'source_title' => 'کسر توسط ادمین',
                                        'source_category' => 'Admin Management',
                                        'payment_flow' => 'wallet_to_admin',
                                        'user_type' => 'admin',
                                        'transaction_context' => 'admin_wallet_deduction',
                                        'admin_user_id' => Auth::id(),
                                        'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                                        'reason_type' => $data['reason_type'],
                                        'force_deduction' => false,
                                        'performed_at' => now()->toISOString()
                                    ]
                                ]);
                            }

                            // Log the action
                            \Illuminate\Support\Facades\Log::info('Admin wallet deduction', [
                                'target_user_id' => $record->id,
                                'target_user_name' => $record->name,
                                'amount' => $data['amount'],
                                'description' => $data['description'],
                                'reason_type' => $data['reason_type'],
                                'force_deduction' => $data['force_deduction'],
                                'admin_user_id' => Auth::id(),
                                'admin_user_name' => Auth::user()?->name ?? 'Unknown',
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('کسر از کیف پول موفق')
                                ->body("مبلغ " . number_format($data['amount']) . " تومان از کیف پول {$record->name} کسر شد.")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('خطا در کسر از کیف پول')
                                ->body('متاسفانه خطایی رخ داد: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('کسر از کیف پول')
                    ->modalDescription('آیا مطمئن هستید که می‌خواهید از کیف پول این کاربر مبلغی کسر کنید؟')
                    ->modalSubmitActionLabel('تایید کسر'),
                Tables\Actions\Action::make('view_wallet_history')
                    ->label('تاریخچه کیف پول')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->url(fn (User $record): string => route('filament.access.resources.wallet-transactions.index', [
                        'tableFilters[user][value]' => $record->id
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function enablePasswordUpdates(bool | Closure $condition = true): void
    {
        static::$enablePasswordUpdates = $condition;
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
