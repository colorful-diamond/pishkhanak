<?php

namespace App\Livewire;

use App\Models\AiContent;
use App\Jobs\GenerateAiContentJob;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiContentStatus extends Component
{
    use WithPagination;
    
    public $selectedContent = null;
    public $showDetails = false;
    public $filter = 'all'; // all, background, realtime, completed, failed, processing
    public $searchTerm = '';
    
    protected $listeners = ['refreshStatus' => '$refresh'];
    
    public function mount()
    {
        // Auto-refresh every 5 seconds if there are processing jobs
        $this->dispatch('startPolling');
    }
    
    public function render()
    {
        $query = AiContent::where('author_id', Auth::id())
            ->orderBy('created_at', 'desc');
            
        // Apply filters
        switch ($this->filter) {
            case 'background':
                $query->where('is_background', true);
                break;
            case 'realtime':
                $query->where('is_background', false);
                break;
            case 'completed':
                $query->where('status', 'completed');
                break;
            case 'failed':
                $query->whereIn('status', ['failed']);
                break;
            case 'processing':
                $query->where('status', 'generating');
                break;
        }
        
        // Apply search
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('short_description', 'like', '%' . $this->searchTerm . '%');
            });
        }
        
        $contents = $query->paginate(10);
        
        return view('livewire.ai-content-status', [
            'contents' => $contents
        ]);
    }
    
    public function viewDetails($contentId)
    {
        $this->selectedContent = AiContent::find($contentId);
        $this->showDetails = true;
    }
    
    public function closeDetails()
    {
        $this->selectedContent = null;
        $this->showDetails = false;
    }
    
    public function retryGeneration($contentId)
    {
        $content = AiContent::find($contentId);
        
        if (!$content || $content->author_id !== Auth::id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'PERSIAN_TEXT_1e4fc12e'
            ]);
            return;
        }
        
        if (!$content->canRetry()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'PERSIAN_TEXT_56a1d238'
            ]);
            return;
        }
        
        // Reset status and dispatch job
        $content->update([
            'status' => 'generating',
            'job_status' => 'pending',
            'error_message' => null,
            'failed_at' => null,
            'generation_progress' => 0
        ]);
        
        // Determine which step failed and restart from there
        $step = $this->determineFailedStep($content);
        
        // Dispatch the job
        $job = GenerateAiContentJob::dispatch($content->id, $step, $content->generation_settings);
        
        $content->update([
            'job_id' => $job->getJobId()
        ]);
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'PERSIAN_TEXT_680f7874'
        ]);
        
        $this->dispatch('refreshStatus');
    }
    
    protected function determineFailedStep($content)
    {
        if (!$content->headings_generated_at) return 'headings';
        if (!$content->sections_generated_at) return 'sections';
        if (!$content->summary_generated_at) return 'summary';
        if (!$content->meta_generated_at) return 'meta';
        if (!$content->faq_generated_at) return 'faq';
        
        return 'headings'; // Start from beginning if unclear
    }
    
    public function deleteContent($contentId)
    {
        $content = AiContent::find($contentId);
        
        if (!$content || $content->author_id !== Auth::id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'PERSIAN_TEXT_1e4fc12e'
            ]);
            return;
        }
        
        $content->delete();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'PERSIAN_TEXT_c6ce0841'
        ]);
        
        $this->dispatch('refreshStatus');
    }
    
    public function continueEditing($contentId)
    {
        return redirect()->route('filament.access.pages.ai-content-generator', [
            'resume' => $contentId
        ]);
    }
    
    public function downloadContent($contentId)
    {
        $content = AiContent::find($contentId);
        
        if (!$content || $content->author_id !== Auth::id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'PERSIAN_TEXT_1e4fc12e'
            ]);
            return;
        }
        
        $html = $content->generateUnifiedHtml(true, 'article', true, true);
        
        return response()->streamDownload(function () use ($html, $content) {
            echo $html;
        }, $content->slug . '.html', [
            'Content-Type' => 'text/html; charset=UTF-8'
        ]);
    }
    
    public function getProgressColor($progress)
    {
        if ($progress < 30) return 'bg-red-500';
        if ($progress < 60) return 'bg-yellow-500';
        if ($progress < 90) return 'bg-blue-500';
        return 'bg-green-500';
    }
    
    public function getStatusColor($status)
    {
        return match($status) {
            'completed' => 'text-green-600 bg-green-100',
            'generating' => 'text-blue-600 bg-blue-100',
            'failed' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
    
    public function getStatusIcon($status)
    {
        return match($status) {
            'completed' => 'M5 13l4 4L19 7',
            'generating' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'failed' => 'M6 18L18 6M6 6l12 12',
            default => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        };
    }
}