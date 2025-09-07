<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketTagController extends Controller
{
    /**
     * Display a listing of the tags
     */
    public function index()
    {
        $tags = TicketTag::withCount('tickets')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);
            
        return view('admin.ticket-tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag
     */
    public function create()
    {
        $colorOptions = TicketTag::getColorOptions();
        return view('admin.ticket-tags.create', compact('colorOptions'));
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ticket_tags,name',
            'color' => 'required|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'emoji' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        TicketTag::create($validated);

        return redirect()->route('admin.ticket-tags.index')
            ->with('success', 'PERSIAN_TEXT_9e9941a6');
    }

    /**
     * Display the specified tag
     */
    public function show(TicketTag $ticketTag)
    {
        $ticketTag->load('tickets.user');
        return view('admin.ticket-tags.show', compact('ticketTag'));
    }

    /**
     * Show the form for editing the tag
     */
    public function edit(TicketTag $ticketTag)
    {
        $colorOptions = TicketTag::getColorOptions();
        return view('admin.ticket-tags.edit', compact('ticketTag', 'colorOptions'));
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, TicketTag $ticketTag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ticket_tags,name,' . $ticketTag->id,
            'color' => 'required|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'emoji' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $ticketTag->update($validated);

        return redirect()->route('admin.ticket-tags.index')
            ->with('success', 'PERSIAN_TEXT_12991938');
    }

    /**
     * Remove the specified tag
     */
    public function destroy(TicketTag $ticketTag)
    {
        // Check if tag is used
        if ($ticketTag->tickets()->count() > 0) {
            return redirect()->back()
                ->with('error', 'PERSIAN_TEXT_6f7ed7ff');
        }

        $ticketTag->delete();

        return redirect()->route('admin.ticket-tags.index')
            ->with('success', 'PERSIAN_TEXT_35f0b1b5');
    }
}