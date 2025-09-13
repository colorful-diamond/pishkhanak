<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketTemplate;
use App\Models\TicketMessage;
use App\Models\TicketActivity;
// use App\Models\SupportAgent; // Removed - no longer needed
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Current ticket being viewed/edited
    public ?Ticket $selectedTicket = null;
    public $showTicketModal = false;

    // Response form
    public $responseMessage = '';
    public $selectedTemplate = '';
    public $isInternalNote = false;
    public $attachments = [];
    public $newStatus = '';
    public $newPriority = '';
    public $assignToAgent = '';

    // Filters
    public $search = '';
    public $searchType = 'all'; // all, ticket, user, content
    public $dateFrom = '';
    public $dateTo = '';
    public $filterStatus = '';
    public $filterCategory = '';
    public $filterPriority = '';
    public $filterAssigned = '';
    public $showOnlyMine = false;
    public $showOverdue = false;

    // View settings
    public $perPage = 25;
    public $viewMode = 'list';

    // Agent info
    public $currentAgent;

    protected $listeners = [
        'ticketSelected' => 'openTicket',
        'refreshTickets' => '$refresh',
        'templateSelected' => 'useTemplate',
    ];

    protected $rules = [
        'responseMessage' => 'required|min:10|max:5000',
        'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip,rar',
    ];

    public function mount()
    {
        // Support agent functionality removed
        // $this->currentAgent = SupportAgent::where('user_id', Auth::id())->first();
        
        // if ($this->currentAgent) {
        //     $this->currentAgent->updateActivity();
        // }
        
        $this->currentAgent = null;
    }

    public function render()
    {
        return view('livewire.admin.ticket-management', [
            'tickets' => $this->getFilteredTickets(),
            'categories' => TicketCategory::active()->sorted()->get(),
            'priorities' => TicketPriority::active()->sorted()->get(),
            'statuses' => TicketStatus::active()->sorted()->get(),
            'agents' => User::role(['admin', 'support'])->get(),
            'templates' => TicketTemplate::active()->public()->sorted()->get(),
            'dashboardStats' => $this->getDashboardStats(),
        ]);
    }

    private function getFilteredTickets()
    {
        $query = Ticket::with(['user', 'ticketCategory', 'ticketPriority', 'ticketStatus', 'assignedTo', 'messages'])
            ->latest('updated_at');

        // Enhanced search functionality
        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function($q) use ($searchTerm) {
                switch ($this->searchType) {
                    case 'ticket':
                        $q->where('ticket_number', 'like', "%{$searchTerm}%")
                          ->orWhere('subject', 'like', "%{$searchTerm}%")
                          ->orWhere('description', 'like', "%{$searchTerm}%");
                        break;
                    case 'user':
                        $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$searchTerm}%")
                                                          ->orWhere('email', 'like', "%{$searchTerm}%"));
                        break;
                    case 'content':
                        $q->whereHas('messages', fn($mq) => $mq->where('message', 'like', "%{$searchTerm}%"));
                        break;
                    default: // 'all'
                        $q->where('ticket_number', 'like', "%{$searchTerm}%")
                          ->orWhere('subject', 'like', "%{$searchTerm}%")
                          ->orWhere('description', 'like', "%{$searchTerm}%")
                          ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$searchTerm}%")
                                                            ->orWhere('email', 'like', "%{$searchTerm}%"))
                          ->orWhereHas('messages', fn($mq) => $mq->where('message', 'like', "%{$searchTerm}%"));
                        break;
                }
            });
        }

        // Date range filtering
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->filterStatus) {
            $query->where('status_id', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->filterPriority) {
            $query->where('priority_id', $this->filterPriority);
        }

        if ($this->filterAssigned === 'unassigned') {
            $query->whereNull('assigned_to');
        } elseif ($this->filterAssigned) {
            $query->where('assigned_to', $this->filterAssigned);
        }

        if ($this->showOnlyMine) {
            $query->where('assigned_to', Auth::id());
        }

        if ($this->showOverdue) {
            $query->whereHas('ticketStatus', fn($q) => $q->where('slug', '!=', 'closed'))
                  ->where('created_at', '<', now()->subHours(24))
                  ->whereNull('first_response_at');
        }

        return $query->paginate($this->perPage);
    }

    private function getDashboardStats()
    {
        return [
            'total' => Ticket::count(),
            'open' => Ticket::whereHas('ticketStatus', fn($q) => $q->where('slug', 'open'))->count(),
            'overdue' => $this->getOverdueCount(),
            'myTickets' => Ticket::where('assigned_to', Auth::id())->count(),
            'unassigned' => Ticket::whereNull('assigned_to')->count(),
        ];
    }

    private function getOverdueCount()
    {
        return Ticket::whereHas('ticketStatus', fn($q) => $q->where('slug', 'open'))
                    ->where('created_at', '<', now()->subHours(24))
                    ->whereNull('first_response_at')
                    ->count();
    }

    public function openTicket($ticketId)
    {
        $this->selectedTicket = Ticket::with([
            'user', 'ticketCategory', 'ticketPriority', 'ticketStatus', 'assignedTo',
            'messages.user', 'messages.attachments', 'activities.user'
        ])->findOrFail($ticketId);
        
        $this->newStatus = $this->selectedTicket->status_id;
        $this->newPriority = $this->selectedTicket->priority_id;
        $this->assignToAgent = $this->selectedTicket->assigned_to;
        
        $this->showTicketModal = true;
    }

    public function closeTicketModal()
    {
        $this->showTicketModal = false;
        $this->selectedTicket = null;
        $this->reset(['responseMessage', 'selectedTemplate', 'isInternalNote', 'attachments', 'newStatus', 'newPriority', 'assignToAgent']);
    }

    public function useTemplate($templateId)
    {
        $template = TicketTemplate::find($templateId);
        if ($template && $this->selectedTicket) {
            $this->responseMessage = $template->processContent([
                'user_name' => $this->selectedTicket->user->name,
                'ticket_number' => $this->selectedTicket->ticket_number,
                'agent_name' => Auth::user()->name,
            ]);

            if ($template->auto_change_status_to) {
                $this->newStatus = $template->auto_change_status_to;
            }

            $template->incrementUsage();
        }
    }

    public function assignToMe()
    {
        if ($this->selectedTicket && $this->currentAgent && $this->currentAgent->isAvailableForAssignment()) {
            $this->currentAgent->assignTicket($this->selectedTicket);
            $this->assignToAgent = Auth::id();
            
            TicketActivity::log(
                $this->selectedTicket,
                'assigned',
                'تیکت به ' . Auth::user()->name . ' اختصاص داده شد'
            );

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'تیکت به شما اختصاص داده شد'
            ]);
        }
    }

    public function submitResponse()
    {
        $this->validate();

        if (!$this->selectedTicket) return;

        // Create the message
        $message = $this->selectedTicket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $this->responseMessage,
            'is_internal' => $this->isInternalNote,
            'template_id' => $this->selectedTemplate ?: null,
        ]);

        // Handle file uploads
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('tickets/' . $this->selectedTicket->id, $filename, 'public');

                $this->selectedTicket->attachments()->create([
                    'ticket_message_id' => $message->id,
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'file_path' => $path,
                ]);
            }
        }

        // Update ticket properties if changed
        $changes = [];
        
        if ($this->newStatus != $this->selectedTicket->status_id) {
            $oldStatus = $this->selectedTicket->status?->name;
            $this->selectedTicket->update(['status_id' => $this->newStatus]);
            $newStatus = TicketStatus::find($this->newStatus)?->name;
            $changes['status'] = ['old' => $oldStatus, 'new' => $newStatus];
        }

        if ($this->newPriority != $this->selectedTicket->priority_id) {
            $oldPriority = $this->selectedTicket->priority?->name;
            $this->selectedTicket->update(['priority_id' => $this->newPriority]);
            $newPriority = TicketPriority::find($this->newPriority)?->name;
            $changes['priority'] = ['old' => $oldPriority, 'new' => $newPriority];
        }

        if ($this->assignToAgent != $this->selectedTicket->assigned_to) {
            $oldAgent = $this->selectedTicket->assignedTo?->name ?? 'بدون مسئول';
            $this->selectedTicket->update(['assigned_to' => $this->assignToAgent ?: null]);
            $newAgent = User::find($this->assignToAgent)?->name ?? 'بدون مسئول';
            $changes['assigned'] = ['old' => $oldAgent, 'new' => $newAgent];
        }

        // Set first response time if this is the first staff response
        if (!$this->selectedTicket->first_response_at && !$this->isInternalNote) {
            $this->selectedTicket->update(['first_response_at' => now()]);
        }

        // Log activities
        TicketActivity::log(
            $this->selectedTicket,
            'message_added',
            $this->isInternalNote ? 'یادداشت داخلی اضافه شد' : 'پاسخ جدید ارسال شد'
        );

        foreach ($changes as $field => $change) {
            TicketActivity::log(
                $this->selectedTicket,
                $field . '_changed',
                "{$field} از {$change['old']} به {$change['new']} تغییر کرد"
            );
        }

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'پاسخ با موفقیت ارسال شد'
        ]);

        // Refresh the ticket data
        $this->selectedTicket->refresh();
        $this->selectedTicket->load(['messages.user', 'messages.attachments', 'activities.user']);

        // Reset form
        $this->reset(['responseMessage', 'selectedTemplate', 'attachments']);
    }

    public function quickStatusChange($ticketId, $statusId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $oldStatus = $ticket->status?->name;
        $ticket->update(['status_id' => $statusId]);
        $newStatus = TicketStatus::find($statusId)?->name;

        TicketActivity::log(
            $ticket,
            'status_changed',
            "وضعیت از {$oldStatus} به {$newStatus} تغییر کرد"
        );

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'وضعیت تیکت تغییر کرد'
        ]);
    }

    public function quickAssign($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        
        if ($this->currentAgent && $this->currentAgent->isAvailableForAssignment()) {
            $this->currentAgent->assignTicket($ticket);
            
            TicketActivity::log(
                $ticket,
                'assigned',
                'تیکت به ' . Auth::user()->name . ' اختصاص داده شد'
            );

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'تیکت به شما اختصاص داده شد'
            ]);
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'searchType', 'dateFrom', 'dateTo', 'filterStatus', 'filterCategory', 'filterPriority', 'filterAssigned', 'showOnlyMine', 'showOverdue']);
        $this->resetPage();
    }

    public function toggleOverdue()
    {
        $this->showOverdue = !$this->showOverdue;
        $this->resetPage();
    }

    public function quickSearch($type)
    {
        $this->searchType = $type;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleMyTickets()
    {
        $this->showOnlyMine = !$this->showOnlyMine;
        $this->resetPage();
    }

    public function getTimeAgo($datetime)
    {
        return $datetime->diffForHumans();
    }

    public function getPriorityColor($priority)
    {
        if (!$priority) return 'gray';
        
        // Handle both string (old system) and object (new system)
        if (is_string($priority)) {
            return match($priority) {
                'urgent' => 'red',
                'high' => 'orange',
                'medium' => 'yellow',
                'low' => 'blue',
                default => 'gray'
            };
        }
        
        // Handle TicketPriority model object
        if (is_object($priority) && isset($priority->level)) {
        return match($priority->level) {
            9, 10 => 'red',
            7, 8 => 'orange', 
            5, 6 => 'yellow',
            3, 4 => 'blue',
            default => 'green'
        };
        }
        
        return 'gray';
    }

    public function getStatusColor($status)
    {
        if (!$status) return 'gray';
        
        // Handle both string (old system) and object (new system)
        if (is_string($status)) {
            return match($status) {
                'closed' => 'gray',
                'resolved' => 'green',
                'waiting_for_user' => 'purple',
                'in_progress' => 'yellow',
                'open' => 'blue',
                default => 'gray'
            };
        }
        
        // Handle TicketStatus model object
        if (is_object($status)) {
            if (isset($status->is_closed) && $status->is_closed) return 'gray';
            if (isset($status->is_resolved) && $status->is_resolved) return 'green';
            if (isset($status->requires_user_action) && $status->requires_user_action) return 'purple';
        return 'blue';
        }
        
        return 'gray';
    }
} 