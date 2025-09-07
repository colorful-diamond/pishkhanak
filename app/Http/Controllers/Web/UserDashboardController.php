<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\GatewayTransaction;
use App\Events\TicketCreated;
use App\Events\TicketReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserDashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'total_transactions' => $user->transactions()->count(),
            'total_tickets' => $user->tickets()->count(),
            'open_tickets' => $user->tickets()->open()->count(),
            'wallet_balance' => $user->balance,
        ];

        // Get recent transactions
        $recentTransactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent tickets
        $recentTickets = $user->tickets()
            ->latest()
            ->take(5)
            ->get();

        // Get user's wallet
        $wallet = $user->wallet;

        return view('front.user.dashboard', compact('stats', 'recentTransactions', 'recentTickets', 'wallet'));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('front.user.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($validator->validated());

        return back()->with('success', 'اطلاعات پروفایل با موفقیت بروزرسانی شد.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.current_password' => 'رمز عبور فعلی اشتباه است.',
            'password.confirmed' => 'تکرار رمز عبور جدید مطابقت ندارد.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'رمز عبور با موفقیت تغییر یافت.');
    }

    /**
     * Show tickets list
     */
    public function tickets(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->tickets()->with(['latestMessage', 'assignedTo']);

        // Apply filters
        if ($request->filled('status')) {
            if ($request->status === 'open') {
                $query->open();
            } elseif ($request->status === 'resolved') {
                $query->resolved();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('front.user.tickets.index', compact('tickets'));
    }

    /**
     * Show create ticket form
     */
    public function createTicket()
    {
        return view('front.user.tickets.create');
    }

    /**
     * Store new ticket
     */
    public function storeTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|in:technical,billing,general,bug_report,feature_request',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip,rar',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        // Create ticket with default priority 'low'
        $ticket = $user->tickets()->create([
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => 'low', // Default priority set to low - managed by admin panel
            'category' => $request->category,
        ]);

        // Create initial message
        $message = $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $request->description,
            'is_internal' => false,
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('tickets/' . $ticket->id, $filename, 'public');

                $ticket->attachments()->create([
                    'ticket_message_id' => $message->id,
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'file_path' => $path,
                ]);
            }
        }

        // Fire event for notifications
        event(new TicketCreated($ticket));

        return redirect()->route('app.user.tickets.show', $ticket)
            ->with('success', 'درخواست شما با موفقیت ایجاد شد.');
    }

    /**
     * Show ticket details
     */
    public function showTicket(Ticket $ticket)
    {
        // Ensure user owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        // Load ticket relationships including message attachments
        $ticket->load(['messages.user', 'messages.attachments', 'attachments', 'assignedTo']);

        return view('front.user.tickets.show', compact('ticket'));
    }

    /**
     * Add message to ticket
     */
    public function addMessage(Request $request, Ticket $ticket)
    {
        // Ensure user owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip,rar',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        // Create message
        $message = $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $request->message,
            'is_internal' => false,
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('tickets/' . $ticket->id, $filename, 'public');

                $ticket->attachments()->create([
                    'ticket_message_id' => $message->id,
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'file_path' => $path,
                ]);
            }
        }

        // Update ticket status if it was waiting for user
        if ($ticket->status === 'waiting_for_user') {
            $ticket->update(['status' => 'open']);
        }

        // Fire event for notifications
        event(new TicketReplied($ticket, $message));

        return back()->with('success', 'پیام شما با موفقیت ارسال شد.');
    }

    /**
     * Close ticket
     */
    public function closeTicket(Ticket $ticket)
    {
        // Ensure user owns this ticket
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'درخواست با موفقیت بسته شد.');
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(TicketAttachment $attachment)
    {
        // Ensure user owns this ticket
        if ($attachment->ticket->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_filename);
    }
} 