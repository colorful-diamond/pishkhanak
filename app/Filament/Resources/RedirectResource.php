<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?string $navigationLabel = 'مدیریت تغییر مسیر';
    
    protected static ?string $modelLabel = 'تغییر مسیر';
    
    protected static ?string $pluralModelLabel = 'تغییر مسیرها';

    protected static ?string $navigationGroup = 'مدیریت محتوا';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات تغییر مسیر')
                    ->schema([
                        TextInput::make('from_url')
                            ->label('URL مبدأ')
                            ->placeholder('/services/old-service')
                            ->helperText('URL قدیمی که باید تغییر مسیر داده شود (بدون دامنه)')
                            ->required()
                            ->maxLength(500)
                            ->rules(['regex:/^\/.*/']),

                        TextInput::make('to_url')
                            ->label('URL مقصد')
                            ->placeholder('/services/new-service یا https://example.com')
                            ->helperText('URL جدید که کاربر به آن هدایت می‌شود')
                            ->required()
                            ->maxLength(500),

                        Select::make('status_code')
                            ->label('کد وضعیت HTTP')
                            ->options([
                                301 => '301 - Moved Permanently (انتقال دائمی)',
                                302 => '302 - Found (انتقال موقت)',
                                303 => '303 - See Other',
                                307 => '307 - Temporary Redirect',
                                308 => '308 - Permanent Redirect',
                            ])
                            ->default(301)
                            ->required()
                            ->helperText('301 برای انتقال دائمی، 302 برای انتقال موقت'),

                        Toggle::make('is_exact_match')
                            ->label('تطابق دقیق')
                            ->helperText('فعال: تطابق دقیق URL / غیرفعال: تطابق الگویی (wildcards)')
                            ->default(true),

                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),

                        Textarea::make('description')
                            ->label('توضیحات')
                            ->placeholder('توضیح کوتاهی از دلیل این تغییر مسیر')
                            ->maxLength(255)
                            ->rows(3),
                    ])->columns(2),

                Section::make('اطلاعات آماری')
                    ->schema([
                        TextInput::make('hit_count')
                            ->label('تعداد استفاده')
                            ->disabled()
                            ->default(0),

                        TextInput::make('last_hit_at')
                            ->label('آخرین استفاده')
                            ->disabled(),
                    ])->columns(2)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_url')
                    ->label('URL مبدأ')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(50),

                TextColumn::make('to_url')
                    ->label('URL مقصد')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(50),

                TextColumn::make('status_code')
                    ->label('کد وضعیت')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '301' => 'success',
                        '302' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state)
                    ->sortable(),

                IconColumn::make('is_exact_match')
                    ->label('تطابق دقیق')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('hit_count')
                    ->label('تعداد کلیک')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('last_hit_at')
                    ->label('آخرین استفاده')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->placeholder('استفاده نشده'),

                TextColumn::make('creator.name')
                    ->label('ایجاد شده توسط')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_code')
                    ->label('کد وضعیت')
                    ->options([
                        301 => '301 - دائمی',
                        302 => '302 - موقت',
                        303 => '303 - See Other',
                        307 => '307 - Temporary',
                        308 => '308 - Permanent',
                    ]),

                Filter::make('is_active')
                    ->label('فقط فعال‌ها')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->default(),

                Filter::make('unused')
                    ->label('استفاده نشده')
                    ->query(fn (Builder $query): Builder => $query->where('hit_count', 0)),

                Filter::make('popular')
                    ->label('پرکاربرد')
                    ->query(fn (Builder $query): Builder => $query->where('hit_count', '>', 10)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('clear_cache')
                    ->label('پاک کردن کش')
                    ->icon('heroicon-o-trash')
                    ->color('warning')
                    ->action(function ($record) {
                        \App\Models\Redirect::clearUrlCache($record->from_url);
                        \Filament\Notifications\Notification::make()
                            ->title('کش پاک شد')
                            ->body("کش URL {$record->from_url} پاک شد")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('اطلاعات تغییر مسیر')
                    ->schema([
                        TextEntry::make('from_url')
                            ->label('URL مبدأ')
                            ->copyable(),

                        TextEntry::make('to_url')
                            ->label('URL مقصد')
                            ->copyable(),

                        TextEntry::make('status_code')
                            ->label('کد وضعیت')
                            ->formatStateUsing(fn ($record) => $record->getStatusCodeText()),

                        TextEntry::make('is_exact_match')
                            ->label('نوع تطابق')
                            ->formatStateUsing(fn ($record) => $record->getMatchTypeText()),

                        TextEntry::make('is_active')
                            ->label('وضعیت')
                            ->formatStateUsing(fn ($state) => $state ? 'فعال' : 'غیرفعال')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'gray'),

                        TextEntry::make('description')
                            ->label('توضیحات')
                            ->placeholder('بدون توضیحات'),
                    ])->columns(2),

                \Filament\Infolists\Components\Section::make('آمار استفاده')
                    ->schema([
                        TextEntry::make('hit_count')
                            ->label('تعداد کل استفاده'),

                        TextEntry::make('last_hit_at')
                            ->label('آخرین استفاده')
                            ->dateTime('Y/m/d H:i:s')
                            ->placeholder('استفاده نشده'),

                        TextEntry::make('creator.name')
                            ->label('ایجاد شده توسط')
                            ->placeholder('نامشخص'),

                        TextEntry::make('created_at')
                            ->label('تاریخ ایجاد')
                            ->dateTime('Y/m/d H:i:s'),

                        TextEntry::make('updated_at')
                            ->label('آخرین ویرایش')
                            ->dateTime('Y/m/d H:i:s'),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'view' => Pages\ViewRedirect::route('/{record}'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}