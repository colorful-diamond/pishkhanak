<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterLinkResource\Pages;
use App\Models\FooterLink;
use App\Models\FooterSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FooterLinkResource extends Resource
{
    protected static ?string $model = FooterLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'مدیریت فوتر و لینک‌ها';

    protected static ?string $navigationLabel = 'لینک‌های فوتر';

    protected static ?string $modelLabel = 'لینک فوتر';

    protected static ?string $pluralModelLabel = 'لینک‌های فوتر';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات لینک')
                    ->schema([
                        Forms\Components\Select::make('footer_section_id')
                            ->label('بخش فوتر')
                            ->options(FooterSection::where('is_active', true)->pluck('title', 'id'))
                            ->required()
                            ->searchable()
                            ->placeholder('بخش مورد نظر را انتخاب کنید'),

                        Forms\Components\TextInput::make('title')
                            ->label('عنوان لینک')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: کارت به شبا'),

                        Forms\Components\TextInput::make('url')
                            ->label('آدرس لینک')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: /services/card-iban'),

                        Forms\Components\TextInput::make('icon')
                            ->label('آیکون')
                            ->maxLength(255)
                            ->placeholder('مثال: heroicon-o-credit-card'),
                    ])->columns(2),

                Forms\Components\Section::make('تنظیمات نمایش')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->required(),

                        Forms\Components\Toggle::make('open_in_new_tab')
                            ->label('باز شدن در تب جدید')
                            ->default(false)
                            ->required(),

                        Forms\Components\Select::make('target')
                            ->label('نحوه باز شدن')
                            ->options([
                                '_self' => 'همان صفحه',
                                '_blank' => 'تب جدید',
                                '_parent' => 'صفحه والد',
                                '_top' => 'بالاترین صفحه',
                            ])
                            ->default('_self')
                            ->required(),

                        Forms\Components\KeyValue::make('attributes')
                            ->label('ویژگی‌های HTML')
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
                Tables\Columns\TextColumn::make('section.title')
                    ->label('بخش')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('آدرس')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('icon')
                    ->label('آیکون')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('open_in_new_tab')
                    ->label('تب جدید')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('footer_section_id')
                    ->label('بخش فوتر')
                    ->options(FooterSection::where('is_active', true)->pluck('title', 'id')),

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
            'index' => Pages\ListFooterLinks::route('/'),
            'create' => Pages\CreateFooterLink::route('/create'),
            'edit' => Pages\EditFooterLink::route('/{record}/edit'),
        ];
    }
} 