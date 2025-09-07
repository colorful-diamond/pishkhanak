<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCategoryResource\Pages;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Archilex\AdvancedTables\AdvancedTables; // Import AdvancedTables
use Archilex\AdvancedTables\Filters\AdvancedFilter; // Import AdvancedFilter
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceCategoryResource extends Resource
{
    use AdvancedTables; // Use the AdvancedTables trait

    protected static ?string $model = ServiceCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'مدیریت خدمات';

    protected static ?string $navigationLabel = 'دسته‌بندی خدمات';

    protected static ?string $modelLabel = 'دسته‌بندی خدمت';

    protected static ?string $pluralModelLabel = 'دسته‌بندی خدمات';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/service_category.navigation_group');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::resources/service_category.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-panels::resources/service_category.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام دسته‌بندی')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('نامک')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                        Forms\Components\TextInput::make('display_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('تنظیمات ظاهری')
                    ->schema([
                        Forms\Components\ColorPicker::make('background_color')
                            ->label('رنگ پس‌زمینه')
                            ->default('#f0f9ff'),
                        Forms\Components\ColorPicker::make('border_color')
                            ->label('رنگ حاشیه')
                            ->default('#bbf7d0'),
                        Forms\Components\ColorPicker::make('icon_color')
                            ->label('رنگ آیکون')
                            ->default('#10b981'),
                        Forms\Components\ColorPicker::make('hover_border_color')
                            ->label('رنگ حاشیه هنگام هاور')
                            ->default('#4ade80'),
                        Forms\Components\ColorPicker::make('hover_background_color')
                            ->label('رنگ پس‌زمینه هنگام هاور')
                            ->default('#f0fdf4'),
                    ])->columns(2),

                Forms\Components\Section::make('آیکون پس‌زمینه')
                    ->schema([
                        Forms\Components\Textarea::make('background_icon')
                            ->label('کد SVG آیکون پس‌زمینه')
                            ->rows(10)
                            ->helperText('کد SVG آیکون را اینجا قرار دهید. این آیکون در پس‌زمینه بخش نمایش داده می‌شود.')
                            ->placeholder('<svg>...</svg>'),
                        SpatieMediaLibraryFileUpload::make('background_image')
                            ->label('تصویر پس‌زمینه')
                            ->collection('background_image')
                            ->image()
                            ->disk('thumbnails')
                            ->helperText('تصویری برای پس‌زمینه آپلود کنید. در صورت وجود هم SVG و هم تصویر، تصویر اولویت دارد.')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048), // 2MB
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('نامک')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('display_order')
                    ->label('ترتیب')
                    ->sortable(),
                IconColumn::make('background_icon')
                    ->label('SVG')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->background_icon))
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('background_image')
                    ->label('تصویر')
                    ->collection('background_image')
                    ->size(40)
                    ->circular()
                    ->sortable(),
                TextColumn::make('services_count')
                    ->label('تعداد خدمات')
                    ->counts('services')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('تاریخ بروزرسانی')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('display_order', 'asc');
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
            'index' => Pages\ListServiceCategories::route('/'),
            'create' => Pages\CreateServiceCategory::route('/create'),
            'edit' => Pages\EditServiceCategory::route('/{record}/edit'),
        ];
    }
}