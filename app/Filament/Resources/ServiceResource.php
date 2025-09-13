<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Models\User;
use App\Models\Category;
use App\Services\AiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Filters\AdvancedFilter;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Rules\UniqueServiceSlug;

class ServiceResource extends Resource
{
    use AdvancedTables;

    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/service.navigation_group');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::resources/service.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-panels::resources/service.plural_label');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('filament-panels::resources/service.sections.main_content'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label(__('filament-panels::resources/service.fields.title'))
                                    ->placeholder(__('filament-panels::resources/service.fields.ai_title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, callable $set) {
                                        if($state !== '' && $state !== null){
                                            $aiSlug = app(AiService::class)->generateSlug($state);
                                            $set('slug', $aiSlug);
                                        }
                                    }),
                                Forms\Components\TextInput::make('short_title')
                                    ->label('عنوان کوتاه')
                                    ->placeholder('عنوان کوتاه برای نمایش در فضاهای محدود')
                                    ->maxLength(100)
                                    ->helperText('برای استفاده در صفحه اصلی و مکان‌هایی که فضای کمی داریم'),
                                Forms\Components\TextInput::make('slug')
                                    ->label(__('filament-panels::resources/service.fields.slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(function (?Service $record) {
                                        return [
                                            new \App\Rules\UniqueServiceSlug($record?->id)
                                        ];
                                    }),
                                Forms\Components\TextInput::make('price')
                                    ->label(__('filament-panels::resources/service.fields.price'))
                                    ->integer()
                                    ->default(0)
                                    ->prefix('تومان'),
                                Forms\Components\TextInput::make('cost')
                                    ->label('هزینه سرویس (برای ما)')
                                    ->integer()
                                    ->default(0)
                                    ->prefix('تومان')
                                    ->helperText('هزینه‌ای که برای اجرای این سرویس پرداخت می‌کنیم'),
                                Forms\Components\Select::make('category_id')
                                    ->label(__('filament-panels::resources/service.fields.category'))
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->maxLength(1000),
                                    ]),
                                Forms\Components\Select::make('parent_id')
                                    ->label(__('filament-panels::resources/service.fields.parent'))
                                    ->relationship('parent', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                SpatieTagsInput::make('tags')
                                    ->label(__('filament-panels::resources/service.fields.tags')),
                                
                                Forms\Components\RichEditor::make('content')
                                    ->label(__('filament-panels::resources/service.fields.content'))
                                    ->required()
                                    ->helperText(__('filament-panels::resources/service.fields.content_helper'))
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('summary')
                                    ->label(__('filament-panels::resources/service.fields.summary'))
                                    ->placeholder(__('filament-panels::resources/service.fields.ai_short_description'))
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('description')
                                    ->label(__('filament-panels::resources/service.fields.description'))
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('explanation')
                                    ->label('توضیحات تکمیلی')
                                    ->placeholder('توضیحات تکمیلی و جزئیات بیشتر در مورد سرویس')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make(__('filament-panels::resources/service.sections.media'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('icon')
                                    ->label(__('filament-panels::resources/service.fields.icon'))
                                    ->helperText(__('filament-panels::resources/service.fields.icon_helper_text'))
                                    ->collection('icon')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                    ->maxSize(2048) // 2MB
                                    ->image()
                                    ->disk('thumbnails'),
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->label(__('filament-panels::resources/service.fields.thumbnail'))
                                    ->helperText(__('filament-panels::resources/service.fields.thumbnail_helper_text'))
                                    ->collection('thumbnail')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120) // 5MB
                                    ->image()
                                    ->disk('thumbnails'),
                                SpatieMediaLibraryFileUpload::make('images')
                                    ->label(__('filament-panels::resources/service.fields.images'))
                                    ->collection('gallery')
                                    ->multiple()
                                    ->reorderable()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120) // 5MB
                                    ->image()
                                    ->disk('gallery'),
                                Forms\Components\Select::make('video_type')
                                    ->label(__('filament-panels::resources/service.fields.video_type'))
                                    ->options([
                                        'local' => __('filament-panels::resources/service.fields.local'),
                                        'youtube' => __('filament-panels::resources/service.fields.youtube'),
                                        'aparat' => __('filament-panels::resources/service.fields.aparat'),
                                        'vimeo' => __('filament-panels::resources/service.fields.vimeo'),
                                    ])
                                    ->reactive(),
                                Forms\Components\TextInput::make('video')
                                    ->label(__('filament-panels::resources/service.fields.video'))
                                    ->visible(fn (callable $get) => $get('video_type') !== null)
                                    ->rule(function (callable $get) {
                                        if ($get('video_type') === 'youtube') {
                                            return ['regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/'];
                                        } elseif ($get('video_type') === 'vimeo') {
                                            return ['regex:/^(https?:\/\/)?(www\.)?(vimeo\.com)\/.+$/'];
                                        } elseif ($get('video_type') === 'aparat') {
                                            return ['regex:/^(https?:\/\/)?(www\.)?(aparat\.com)\/.+$/'];
                                        }
                                        return [];
                                    })
                                    ->helperText(function (callable $get) {
                                        if ($get('video_type') === 'local') {
                                            return __('filament-panels::resources/service.fields.local_video_helper_text');
                                        }
                                        return __('filament-panels::resources/service.fields.video_url_helper_text');
                                    }),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('filament-panels::resources/service.sections.publishing'))
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label(__('filament-panels::resources/service.fields.status'))
                                    ->options([
                                        'active' => __('filament-panels::resources/service.fields.active'),
                                        'inactive' => __('filament-panels::resources/service.fields.inactive'),
                                    ])
                                    ->default('active')
                                    ->required(),
                                Forms\Components\Toggle::make('featured')
                                    ->label(__('filament-panels::resources/service.fields.featured')),
                                Forms\Components\Select::make('author_id')
                                    ->label(__('filament-panels::resources/service.fields.author'))
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(auth()->id()),
                                Forms\Components\Placeholder::make('views')
                                    ->label(__('filament-panels::resources/service.fields.views'))
                                    ->content(fn (?Service $record): string => $record?->views ?? '0'),
                                Forms\Components\Placeholder::make('likes')
                                    ->label(__('filament-panels::resources/service.fields.likes'))
                                    ->content(fn (?Service $record): string => $record?->likes ?? '0'),
                                Forms\Components\Placeholder::make('shares')
                                    ->label(__('filament-panels::resources/service.fields.shares'))
                                    ->content(fn (?Service $record): string => $record?->shares ?? '0'),
                            ]),

                        Forms\Components\Section::make('حالت تعمیر و نگهداری')
                            ->schema([
                                Forms\Components\Toggle::make('is_maintenance')
                                    ->label('فعال کردن حالت تعمیر')
                                    ->helperText('در این حالت، کاربران وارد شده نمی‌توانند از سرویس استفاده کنند')
                                    ->reactive(),
                                Forms\Components\Textarea::make('maintenance_message')
                                    ->label('پیام خطای دلخواه')
                                    ->placeholder('این سرویس در حال حاضر در دسترس نمی‌باشد. لطفا بعدا تلاش کنید.')
                                    ->helperText('این پیام به کاربران نمایش داده می‌شود')
                                    ->visible(fn (callable $get) => $get('is_maintenance') === true)
                                    ->rows(3),
                                Forms\Components\DateTimePicker::make('maintenance_ends_at')
                                    ->label('زمان پایان تعمیرات')
                                    ->helperText('اختیاری - سیستم به صورت خودکار بعد از این زمان از حالت تعمیر خارج می‌شود')
                                    ->visible(fn (callable $get) => $get('is_maintenance') === true)
                                    ->minDate(now())
                                    ->native(false),
                                Forms\Components\Toggle::make('maintenance_affects_children')
                                    ->label('اعمال به زیرسرویس‌ها')
                                    ->helperText('آیا حالت تعمیر به تمام زیرسرویس‌ها هم اعمال شود؟')
                                    ->visible(fn (callable $get) => $get('is_maintenance') === true)
                                    ->default(true),
                                Forms\Components\Placeholder::make('maintenance_info')
                                    ->label('اطلاعات تعمیرات')
                                    ->visible(fn (?Service $record) => $record && $record->is_maintenance)
                                    ->content(function (?Service $record): string {
                                        if (!$record || !$record->is_maintenance) {
                                            return '';
                                        }
                                        $html = '<div class="space-y-2">';
                                        if ($record->maintenance_started_at) {
                                            $html .= '<p><strong>شروع تعمیرات:</strong> ' . \Carbon\Carbon::parse($record->maintenance_started_at)->diffForHumans() . '</p>';
                                        }
                                        if ($record->maintenance_affects_children && $record->children()->exists()) {
                                            $affectedCount = $record->getAffectedServices()->count() - 1;
                                            if ($affectedCount > 0) {
                                                $html .= '<p><strong>تعداد زیرسرویس‌های متأثر:</strong> ' . $affectedCount . ' سرویس</p>';
                                            }
                                        }
                                        $html .= '</div>';
                                        return $html;
                                    }),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make(__('filament-panels::resources/service.sections.seo'))
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label(__('filament-panels::resources/service.fields.meta_title'))
                                    ->maxLength(60),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label(__('filament-panels::resources/service.fields.meta_description'))
                                    ->maxLength(160),
                                Forms\Components\TagsInput::make('meta_keywords')
                                    ->label(__('filament-panels::resources/service.fields.meta_keywords'))
                                    ->separator(','),
                                Forms\Components\TextInput::make('og_title')
                                    ->label(__('filament-panels::resources/service.fields.og_title'))
                                    ->maxLength(60),
                                Forms\Components\Textarea::make('og_description')
                                    ->label(__('filament-panels::resources/service.fields.og_description'))
                                    ->maxLength(160),
                                FileUpload::make('og_image')
                                    ->label(__('filament-panels::resources/service.fields.og_image'))
                                    ->directory('images/Services/og')
                                    ->image(),
                                Forms\Components\TextInput::make('twitter_title')
                                    ->label(__('filament-panels::resources/service.fields.twitter_title'))
                                    ->maxLength(60),
                                Forms\Components\Textarea::make('twitter_description')
                                    ->label(__('filament-panels::resources/service.fields.twitter_description'))
                                    ->maxLength(160),
                                FileUpload::make('twitter_image')
                                    ->label(__('filament-panels::resources/service.fields.twitter_image'))
                                    ->directory('images/Services/twitter')
                                    ->image(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make(__('filament-panels::resources/service.sections.schema'))
                            ->schema([
                                Forms\Components\Textarea::make('schema')
                                    ->label(__('filament-panels::resources/service.fields.schema'))
                                    ->helperText('Enter JSON schema data (will be automatically parsed)')
                                    ->rows(10)
                                    ->nullable()
                                    ->formatStateUsing(function ($state) {
                                        return is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state;
                                    })
                                    ->dehydrateStateUsing(function ($state) {
                                        if (empty($state)) {
                                            return null;
                                        }
                                        $decoded = json_decode($state, true);
                                        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                                    })
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('filament-panels::resources/service.sections.services'))
                            ->schema([
                                Repeater::make('related_services')
                                    ->label(__('filament-panels::resources/service.fields.services'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('filament-panels::resources/service.fields.name'))
                                            ->required(),
                                    ]),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make(__('filament-panels::resources/service.sections.faqs'))
                            ->schema([
                                Repeater::make('faqs')
                                    ->label(__('filament-panels::resources/service.fields.faqs'))
                                    ->schema([
                                        Forms\Components\TextInput::make('question')
                                            ->label(__('filament-panels::resources/service.fields.question'))
                                            ->required(),
                                        Forms\Components\RichEditor::make('answer')
                                            ->label(__('filament-panels::resources/service.fields.answer'))
                                            ->required(),
                                    ]),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament-panels::resources/service.fields.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament-panels::resources/service.fields.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('short_title')
                    ->label('عنوان کوتاه')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('explanation')
                    ->label('توضیحات تکمیلی')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (?string $state): ?string {
                        return $state ? strip_tags($state) : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cost')
                    ->label('هزینه سرویس')
                    ->money('IRT')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('filament-panels::resources/service.fields.slug'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament-panels::resources/service.fields.category'))
                    ->searchable()
                    ->sortable(),
                SpatieTagsColumn::make('tags')
                    ->label(__('filament-panels::resources/service.fields.tags')),
                Tables\Columns\TextColumn::make('content_type')
                    ->label(__('filament-panels::resources/service.fields.content_type'))
                    ->getStateUsing(function (Service $record): string {
                        return $record->hasAiContent() ? 'AI Content' : 'Manual';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AI Content' => 'success',
                        'Manual' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'AI Content' => 'محتوای هوش مصنوعی',
                        'Manual' => 'محتوای دستی',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament-panels::resources/service.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('filament-panels::resources/service.fields.published_at'))
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('featured')
                    ->label(__('filament-panels::resources/service.fields.featured'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_maintenance')
                    ->label('حالت تعمیر')
                    ->boolean()
                    ->trueIcon('heroicon-o-wrench-screwdriver')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('filament-panels::resources/service.fields.author'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->label(__('filament-panels::resources/service.fields.views'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-panels::resources/service.fields.created_at'))
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-panels::resources/service.fields.updated_at'))
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                SpatieMediaLibraryImageColumn::make('icon')
                    ->label(__('filament-panels::resources/service.fields.icon'))
                    ->collection('icon'),
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->label(__('filament-panels::resources/service.fields.thumbnail'))
                    ->collection('thumbnail'),
            ])
            ->filters([
                AdvancedFilter::make(),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'active' => __('filament-panels::resources/service.fields.active'),
                        'inactive' => __('filament-panels::resources/service.fields.inactive'),
                    ]),
                TernaryFilter::make('featured')
                    ->label(__('filament-panels::resources/service.fields.featured')),
                TernaryFilter::make('is_maintenance')
                    ->label('حالت تعمیر'),
                SelectFilter::make('author')
                    ->relationship('author', 'name'),
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')->jalali(),
                        Forms\Components\DatePicker::make('published_until')->jalali(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('set_ai_content')
                    ->label(__('filament-panels::resources/service.actions.set_ai_content'))
                    ->icon('heroicon-o-sparkles')
                    ->form([
                        Forms\Components\Select::make('ai_content_id')
                            ->label(__('filament-panels::resources/service.fields.ai_content'))
                            ->options(function () {
                                return \App\Models\AiContent::where('status', 'completed')
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (Service $record, array $data) {
                        $record->update(['content' => $data['ai_content_id']]);
                        
                        Notification::make()
                            ->title(__('filament-panels::resources/service.notifications.ai_content_set'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label(__('filament-panels::resources/service.actions.duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Service $record) {
                        $newService = $record->replicate();
                        $newService->title = $newService->title . ' (Copy)';
                        $newService->slug = $newService->slug . '-copy';
                        $newService->published_at = now();
                        $newService->views = 0;
                        $newService->likes = 0;
                        $newService->shares = 0;
                        $newService->save();

                        Notification::make()
                            ->title(__('filament-panels::resources/service.notifications.Service_duplicated'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('toggle_maintenance')
                    ->label(fn (Service $record) => $record->is_maintenance ? 'غیرفعال کردن حالت تعمیر' : 'فعال کردن حالت تعمیر')
                    ->icon(fn (Service $record) => $record->is_maintenance ? 'heroicon-o-check-circle' : 'heroicon-o-wrench-screwdriver')
                    ->color(fn (Service $record) => $record->is_maintenance ? 'success' : 'warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Service $record) => $record->is_maintenance ? 'غیرفعال کردن حالت تعمیر' : 'فعال کردن حالت تعمیر')
                    ->modalDescription(fn (Service $record) => $record->is_maintenance 
                        ? 'آیا مطمئن هستید که می‌خواهید حالت تعمیر را غیرفعال کنید؟' 
                        : 'با فعال کردن حالت تعمیر، کاربران وارد شده نمی‌توانند از این سرویس استفاده کنند.')
                    ->form(function (Service $record) {
                        if ($record->is_maintenance) {
                            return [];
                        }
                        return [
                            Forms\Components\Textarea::make('maintenance_message')
                                ->label('پیام خطای دلخواه')
                                ->placeholder('این سرویس در حال حاضر در دسترس نمی‌باشد. لطفا بعدا تلاش کنید.')
                                ->rows(3),
                            Forms\Components\DateTimePicker::make('maintenance_ends_at')
                                ->label('زمان پایان تعمیرات')
                                ->helperText('اختیاری - سیستم به صورت خودکار بعد از این زمان از حالت تعمیر خارج می‌شود')
                                ->minDate(now())
                                ->native(false),
                            Forms\Components\Toggle::make('maintenance_affects_children')
                                ->label('اعمال به زیرسرویس‌ها')
                                ->helperText('آیا حالت تعمیر به تمام زیرسرویس‌ها هم اعمال شود؟')
                                ->default(true),
                        ];
                    })
                    ->action(function (Service $record, array $data) {
                        if ($record->is_maintenance) {
                            $record->disableMaintenance();
                            Notification::make()
                                ->title('حالت تعمیر غیرفعال شد')
                                ->success()
                                ->send();
                        } else {
                            $record->enableMaintenance(
                                $data['maintenance_message'] ?? null,
                                $data['maintenance_ends_at'] ?? null,
                                $data['maintenance_affects_children'] ?? true
                            );
                            
                            $affectedCount = $record->getAffectedServices()->count();
                            Notification::make()
                                ->title('حالت تعمیر فعال شد')
                                ->body($affectedCount > 1 ? "تعداد {$affectedCount} سرویس تحت تأثیر قرار گرفت" : null)
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->recordUrl(fn (Service $record): string => static::getUrl('edit', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label(__('filament-panels::resources/service.actions.update_status'))
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label(__('filament-panels::resources/service.fields.status'))
                                ->options([
                                    'active' => __('filament-panels::resources/service.fields.active'),
                                    'inactive' => __('filament-panels::resources/service.fields.inactive'),
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });

                            Notification::make()
                                ->title(__('filament-panels::resources/service.notifications.status_updated'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'content', 'summary', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Service $record */
        return [
            'Category' => $record->category->name,
            'Status' => ucfirst($record->status),
            'Published' => $record->published_at?->toFormattedDateString(),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getWidgets(): array
    {
        return [
            ServiceResource\Widgets\ServiceOverview::class,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('generateSeoMeta')
                ->label(__('filament-panels::resources/service.actions.generate_seo_meta'))
                ->icon('heroicon-o-sparkles')
                ->action(function (Service $record, AiService $aiService) {
                    $meta = $aiService->generateMeta($record);

                    $record->update([
                        'meta_title' => $meta['title'],
                        'meta_description' => $meta['description'],
                        'meta_keywords' => $meta['keywords'],
                        'og_title' => $meta['og_title'],
                        'og_description' => $meta['og_description'],
                        'twitter_title' => $meta['twitter_title'],
                        'twitter_description' => $meta['twitter_description'],
                    ]);

                    Notification::make()
                        ->title(__('filament-panels::resources/service.notifications.seo_meta_generated'))
                        ->success()
                        ->send();
                }),
            Action::make('generateSchema')
                ->label(__('filament-panels::resources/service.actions.generate_schema'))
                ->icon('heroicon-o-code-bracket')
                ->action(function (Service $record, AiService $aiService) {
                    $schema = $aiService->generateSchema($record);

                    $record->update([
                        'schema' => $schema['schema'],
                    ]);

                    Notification::make()
                        ->title(__('filament-panels::resources/service.notifications.schema_generated'))
                        ->success()
                        ->send();
                }),
        ];
    }
}