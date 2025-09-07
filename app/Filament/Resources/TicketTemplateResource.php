<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketTemplateResource\Pages;
use App\Filament\Resources\TicketTemplateResource\RelationManagers;
use App\Models\TicketTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketTemplateResource extends Resource
{
    protected static ?string $model = TicketTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'قالب‌های پاسخ';
    
    protected static ?string $modelLabel = 'قالب پاسخ';
    
    protected static ?string $pluralModelLabel = 'قالب‌های پاسخ';

    protected static ?string $navigationGroup = 'پشتیبانی';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات پایه')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('نام قالب')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('پاسخ خوش‌آمدگویی، راهنمای فنی، ...'),
                        Forms\Components\TextInput::make('slug')
                            ->label('شناسه یکتا')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('welcome-response، technical-guide، ...'),
                        Forms\Components\TextInput::make('subject')
                            ->label('موضوع پیش‌فرض')
                            ->maxLength(255)
                            ->placeholder('موضوع پیش‌فرض برای تیکت‌ها'),
                        Forms\Components\Select::make('category_id')
                            ->label('دسته‌بندی')
                            ->relationship('category', 'name')
                            ->placeholder('انتخاب دسته‌بندی'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('محتوا')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('متن قالب')
                            ->required()
                            ->helperText('می‌توانید از متغیرها استفاده کنید: {user_name}, {ticket_id}, {date}')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('variables')
                            ->label('متغیرهای قابل استفاده')
                            ->placeholder('user_name, ticket_id, date, ...')
                            ->helperText('متغیرهای قابل استفاده را با کاما جدا کنید')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('تنظیمات')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->helperText('آیا این قالب فعال است؟')
                            ->required(),
                        Forms\Components\Toggle::make('is_public')
                            ->label('عمومی')
                            ->helperText('آیا همه پشتیبانان بتوانند از این قالب استفاده کنند؟')
                            ->required(),
                        Forms\Components\Select::make('created_by')
                            ->label('ایجاد شده توسط')
                            ->relationship('creator', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('usage_count')
                            ->label('تعداد استفاده')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('تعداد دفعات استفاده از این قالب'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('auto_close_ticket')
                            ->label('بستن خودکار تیکت')
                            ->helperText('آیا با استفاده از این قالب، تیکت خودکار بسته شود؟'),
                        Forms\Components\Select::make('auto_change_status_to')
                            ->label('تغییر خودکار وضعیت به')
                            ->relationship('autoChangeStatus', 'name')
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
                    ->label('نام قالب')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('subject')
                    ->label('موضوع')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('دسته‌بندی')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('ایجاد شده توسط')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('عمومی')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('تعداد استفاده')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('شناسه')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('auto_close_ticket')
                    ->label('بستن خودکار')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('autoChangeStatus.name')
                    ->label('تغییر وضعیت به')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('ندارد'),
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
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('نوع دسترسی')
                    ->placeholder('همه')
                    ->trueLabel('عمومی')
                    ->falseLabel('خصوصی'),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->placeholder('همه دسته‌بندی‌ها'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('مشاهده'),
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
            'index' => Pages\ListTicketTemplates::route('/'),
            'create' => Pages\CreateTicketTemplate::route('/create'),
            'edit' => Pages\EditTicketTemplate::route('/{record}/edit'),
        ];
    }
}
