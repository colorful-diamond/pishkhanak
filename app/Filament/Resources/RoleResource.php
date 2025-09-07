<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'مدیریت کاربران';

    protected static ?string $navigationLabel = 'نقش‌ها و مجوزها';

    public static function getLabel(): string
    {
        return 'نقش';
    }

    public static function getPluralLabel(): string
    {
        return 'نقش‌ها';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام نقش')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('admin, customer, partner'),
                Forms\Components\TextInput::make('guard_name')
                    ->label('نوع محافظ')
                    ->default('web')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام نقش')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'admin' => 'مدیر سیستم',
                        'customer' => 'مشتری',
                        'partner' => 'شریک تجاری',
                        'support' => 'پشتیبان',
                        'content_manager' => 'مدیر محتوا',
                        default => $state
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'admin' => 'danger',
                        'customer' => 'gray',
                        'partner' => 'success',
                        'support' => 'info',
                        'content_manager' => 'warning',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('نوع محافظ')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('تعداد کاربران')
                    ->counts('users')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف'),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
} 