<?php

namespace App\Filament\Pages;

use App\Services\ThumbnailGeneratorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class ManageBackgroundImages extends Page
{
    use WithFileUploads;

    protected static ?string $navigationGroup = 'PERSIAN_TEXT_2a4a3388';
    protected static ?string $navigationLabel = 'PERSIAN_TEXT_88d943dc';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.manage-background-images';
    
    public $backgroundUpload = [];
    public $backgrounds = [];
    
    public function mount(): void
    {
        $this->loadBackgrounds();
        $this->backgroundUpload = []; // Ensure it's initialized as an array
    }
    
    public function loadBackgrounds(): void
    {
        $service = app(ThumbnailGeneratorService::class);
        $this->backgrounds = $service->getBackgrounds();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('PERSIAN_TEXT_63e69ec7')
                    ->schema([
                        Forms\Components\FileUpload::make('backgroundUpload')
                            ->label('PERSIAN_TEXT_5e0f73b6')
                            ->multiple() // Enable multiple file uploads
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1200:630')
                            ->imageResizeTargetWidth(1200)
                            ->imageResizeTargetHeight(630)
                            ->maxSize(5120) // 5MB per file
                            ->maxFiles(10) // Maximum 10 files at once
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('PERSIAN_TEXT_d20edb21'),
                    ])
                    ->columns(1),
            ]);
    }
    
    public function uploadBackground(): void
    {
        // Check if we have files to upload
        if (empty($this->backgroundUpload)) {
            Notification::make()
                ->warning()
                ->title('PERSIAN_TEXT_e10b547e')
                ->send();
            return;
        }
        
        try {
            $uploadedCount = 0;
            $failedCount = 0;
            
            $backgroundsDir = storage_path('app/public/backgrounds/');
            
            // Ensure backgrounds directory exists
            if (!file_exists($backgroundsDir)) {
                mkdir($backgroundsDir, 0755, true);
            }
            
            // The backgroundUpload contains TemporaryUploadedFile objects or file names
            $files = is_array($this->backgroundUpload) ? $this->backgroundUpload : [$this->backgroundUpload];
            
            foreach ($files as $file) {
                if (empty($file)) {
                    continue;
                }
                
                try {
                    // Check if it's a TemporaryUploadedFile object (from Livewire)
                    if (is_object($file) && method_exists($file, 'store')) {
                        // It's a Livewire TemporaryUploadedFile
                        $extension = $file->getClientOriginalExtension() ?: 'jpg';
                        $newFileName = 'background_' . Str::random(16) . '.' . $extension;
                        
                        // Store the file directly to the backgrounds directory
                        $file->storeAs('backgrounds', $newFileName, 'public');
                        $uploadedCount++;
                        
                        \Log::info('File uploaded via Livewire', [
                            'filename' => $newFileName
                        ]);
                    } elseif (is_string($file)) {
                        // It's a string filename - look in livewire-tmp
                        $tempPath = storage_path('app/livewire-tmp/' . $file);
                        
                        if (file_exists($tempPath)) {
                            $extension = 'jpg'; // Default
                            if (strpos($file, '.png') !== false) $extension = 'png';
                            elseif (strpos($file, '.jpeg') !== false) $extension = 'jpeg';
                            elseif (strpos($file, '.webp') !== false) $extension = 'webp';
                            
                            $newFileName = 'background_' . Str::random(16) . '.' . $extension;
                            $newPath = $backgroundsDir . $newFileName;
                            
                            if (rename($tempPath, $newPath)) {
                                $uploadedCount++;
                                \Log::info('File moved from temp', [
                                    'from' => $file,
                                    'to' => $newFileName
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    \Log::error('Failed to process file', [
                        'error' => $e->getMessage(),
                        'file' => is_object($file) ? get_class($file) : $file
                    ]);
                }
            }
            
            // Show notification based on results
            if ($uploadedCount > 0 && $failedCount === 0) {
                Notification::make()
                    ->success()
                    ->title($uploadedCount > 1 
                        ? "PERSIAN_TEXT_3c42cac3" 
                        : 'PERSIAN_TEXT_afaf34d8')
                    ->send();
            } elseif ($uploadedCount > 0 && $failedCount > 0) {
                Notification::make()
                    ->warning()
                    ->title("PERSIAN_TEXT_f81ea3ac")
                    ->send();
            } else {
                Notification::make()
                    ->danger()
                    ->title('PERSIAN_TEXT_e0e2221c')
                    ->send();
            }
            
            // Reset the form field
            $this->reset('backgroundUpload');
            
            // Reload backgrounds to show new uploads
            $this->loadBackgrounds();
            
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('PERSIAN_TEXT_0032c6c5')
                ->body($e->getMessage())
                ->send();
            
            \Log::error('Background upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function deleteBackground(string $path): void
    {
        try {
            // Don't allow deletion of gradient backgrounds
            if (str_contains($path, 'gradient_')) {
                Notification::make()
                    ->warning()
                    ->title('PERSIAN_TEXT_2ea7bd01')
                    ->send();
                return;
            }
            
            // Delete file
            $relativePath = str_replace(storage_path('app/public/'), '', $path);
            Storage::disk('public')->delete($relativePath);
            
            Notification::make()
                ->success()
                ->title('PERSIAN_TEXT_7ba1d3a0')
                ->send();
                
            $this->loadBackgrounds();
            
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('PERSIAN_TEXT_cb762249')
                ->body($e->getMessage())
                ->send();
        }
    }
    
    public function previewThumbnail(string $backgroundPath): void
    {
        try {
            $service = app(ThumbnailGeneratorService::class);
            
            $thumbnailPath = $service->generateThumbnail(
                mainTitle: 'PERSIAN_TEXT_6a0da637',
                subtitle: 'PERSIAN_TEXT_17a72d10',
                backgroundPath: $backgroundPath
            );
            
            $url = Storage::url($thumbnailPath);
            
            $this->dispatch('open-modal', id: 'preview-modal', data: ['url' => $url]);
            
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('PERSIAN_TEXT_d0a67255')
                ->body($e->getMessage())
                ->send();
        }
    }
}