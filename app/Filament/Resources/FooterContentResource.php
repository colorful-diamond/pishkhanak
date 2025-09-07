<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterContentResource\Pages;
use App\Models\FooterContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FooterContentResource extends Resource
{
    protected static ?string $model = FooterContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'مدیریت فوتر و لینک‌ها';

    protected static ?string $navigationLabel = 'محتوای فوتر';

    protected static ?string $modelLabel = 'محتوای فوتر';

    protected static ?string $pluralModelLabel = 'محتوای فوتر';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات محتوا')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('کلید')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('مثال: company_name'),

                        Forms\Components\Select::make('section')
                            ->label('بخش')
                            ->options([
                                'general' => 'عمومی',
                                'contact' => 'اطلاعات تماس',
                                'social' => 'شبکه‌های اجتماعی',
                                'legal' => 'قوانین و مقررات',
                            ])
                            ->default('general')
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('نوع محتوا')
                            ->options([
                                'text' => 'متن ساده',
                                'html' => 'HTML',
                                'image' => 'تصویر',
                                'json' => 'JSON',
                            ])
                            ->default('text')
                            ->required(),

                        Forms\Components\Textarea::make('value')
                            ->label('محتوا')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->placeholder('محتوای مورد نظر را وارد کنید'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->required(),

                        Forms\Components\KeyValue::make('settings')
                            ->label('تنظیمات اضافی')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('کلید')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('محتوا')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('section')
                    ->label('بخش')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'contact' => 'info',
                        'social' => 'success',
                        'legal' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'html' => 'blue',
                        'image' => 'green',
                        'json' => 'purple',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section')
                    ->label('بخش')
                    ->options([
                        'general' => 'عمومی',
                        'contact' => 'اطلاعات تماس',
                        'social' => 'شبکه‌های اجتماعی',
                        'legal' => 'قوانین و مقررات',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع محتوا')
                    ->options([
                        'text' => 'متن ساده',
                        'html' => 'HTML',
                        'image' => 'تصویر',
                        'json' => 'JSON',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت فعال'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key', 'asc');
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
            'index' => Pages\ListFooterContents::route('/'),
            'create' => Pages\CreateFooterContent::route('/create'),
            'edit' => Pages\EditFooterContent::route('/{record}/edit'),
        ];
    }
} 