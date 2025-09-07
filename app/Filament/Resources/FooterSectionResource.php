<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterSectionResource\Pages;
use App\Models\FooterSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FooterSectionResource extends Resource
{
    protected static ?string $model = FooterSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'مدیریت فوتر و لینک‌ها';

    protected static ?string $navigationLabel = 'بخش‌های فوتر';

    protected static ?string $modelLabel = 'بخش فوتر';

    protected static ?string $pluralModelLabel = 'بخش‌های فوتر';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: خدمات بانکی'),

                        Forms\Components\TextInput::make('slug')
                            ->label('شناسه یکتا')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('مثال: banking-services'),

                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->placeholder('توضیحات اختیاری برای این بخش'),

                        Forms\Components\TextInput::make('icon')
                            ->label('آیکون')
                            ->maxLength(255)
                            ->placeholder('مثال: heroicon-o-credit-card'),
                    ])->columns(2),

                Forms\Components\Section::make('تنظیمات نمایش')
                    ->schema([
                        Forms\Components\Select::make('location')
                            ->label('محل نمایش')
                            ->options([
                                'footer' => 'فوتر',
                                'sidebar' => 'نوار کناری',
                                'header' => 'هدر',
                            ])
                            ->default('footer')
                            ->required(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0)
                            ->required(),

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
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('شناسه')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('محل')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'footer' => 'success',
                        'sidebar' => 'info',
                        'header' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('links_count')
                    ->label('تعداد لینک‌ها')
                    ->counts('links')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),

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
                Tables\Filters\SelectFilter::make('location')
                    ->label('محل نمایش')
                    ->options([
                        'footer' => 'فوتر',
                        'sidebar' => 'نوار کناری',
                        'header' => 'هدر',
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
            'index' => Pages\ListFooterSections::route('/'),
            'create' => Pages\CreateFooterSection::route('/create'),
            'edit' => Pages\EditFooterSection::route('/{record}/edit'),
        ];
    }
} 