<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Site Management';
    protected static ?int $navigationSort = 1;

    public static function getPluralLabel(): ?string
    {
        return 'تنظیمات سایت';
    }

    public static function getLabel(): ?string
    {
        return 'تنظیمات';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'مدیریت سایت';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات اصلی')
                    ->schema([
                        TextInput::make('key')
                            ->label('کلید')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('کلید منحصر به فرد برای این تنظیمات'),
                        
                        Select::make('type')
                            ->label('نوع')
                            ->options([
                                'string' => 'متن کوتاه',
                                'text' => 'متن طولانی',
                                'boolean' => 'بله/خیر',
                                'json' => 'JSON',
                                'number' => 'عدد',
                            ])
                            ->required()
                            ->default('string'),
                        
                        Select::make('group')
                            ->label('گروه')
                            ->options([
                                'general' => 'عمومی',
                                'contact' => 'اطلاعات تماس',
                                'business' => 'اطلاعات شرکت',
                                'social' => 'شبکه‌های اجتماعی',
                            ])
                            ->required()
                            ->default('general'),
                    ])
                    ->columns(3),

                Section::make('مقدار و توضیحات')
                    ->schema([
                        TextInput::make('label')
                            ->label('عنوان نمایشی')
                            ->maxLength(255)
                            ->helperText('عنوانی که در فرم‌ها نمایش داده می‌شود'),
                        
                        Textarea::make('description')
                            ->label('توضیحات')
                            ->maxLength(500)
                            ->helperText('توضیحات مربوط به این تنظیمات'),
                        
                        Textarea::make('value')
                            ->label('مقدار')
                            ->required()
                            ->maxLength(1000)
                            ->helperText('مقدار این تنظیمات'),
                    ])
                    ->columns(1),

                Section::make('تنظیمات دسترسی')
                    ->schema([
                        Toggle::make('is_public')
                            ->label('عمومی')
                            ->helperText('آیا این تنظیمات در سایت عمومی نمایش داده شود؟')
                            ->default(true),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('کلید')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('label')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('group')
                    ->label('گروه')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'contact' => 'success',
                        'business' => 'warning',
                        'social' => 'info',
                        default => 'gray',
                    }),
                
                TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->color('gray'),
                
                TextColumn::make('value')
                    ->label('مقدار')
                    ->limit(50)
                    ->searchable(),
                
                IconColumn::make('is_public')
                    ->label('عمومی')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('updated_at')
                    ->label('آخرین بروزرسانی')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('گروه')
                    ->options([
                        'general' => 'عمومی',
                        'contact' => 'اطلاعات تماس',
                        'business' => 'اطلاعات شرکت',
                        'social' => 'شبکه‌های اجتماعی',
                    ]),
                
                SelectFilter::make('type')
                    ->label('نوع')
                    ->options([
                        'string' => 'متن کوتاه',
                        'text' => 'متن طولانی',
                        'boolean' => 'بله/خیر',
                        'json' => 'JSON',
                        'number' => 'عدد',
                    ]),
                
                TernaryFilter::make('is_public')
                    ->label('عمومی')
                    ->placeholder('همه')
                    ->trueLabel('عمومی')
                    ->falseLabel('خصوصی'),
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
            ->defaultSort('group', 'asc');
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
} 