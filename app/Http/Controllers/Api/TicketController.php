<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Support\Carbon;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        $tickets = \App\Models\Ticket::query()
            ->when($request->id, fn($q) => $q->where('id', $request->id))
            ->when($request->customer, fn($q) => $q->whereHas(
                'customer',
                fn($q2) => $q2->where('name', 'like', '%' . $request->customer . '%')
            ))
            ->when($request->subject, fn($q) => $q->where('subject', 'like', '%' . $request->subject . '%'))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->with('customer');

        if ($request->sort && in_array($request->sort, ['id', 'subject', 'status', 'created_at'])) {
            $direction = $request->direction === 'asc' ? 'asc' : 'desc';
            if ($request->sort === 'customer') {
                $tickets->join('customers', 'tickets.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $direction)
                    ->select('tickets.*');
            } else {
                $tickets->orderBy($request->sort, $direction);
            }
        } else {
            $tickets->orderByDesc('created_at');
        }

        $tickets = $tickets->paginate(10)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('customer', 'media');

        return view('tickets.show', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,completed',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Статус тикета обновлён');
    }

    public function store(StoreTicketRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $customer = Customer::firstOrCreate(
                ['email' => $request->email],
                ['name' => $request->name, 'phone' => $request->phone]
            );

            $ticket = $customer->tickets()->create([
                'subject' => $request->subject,
                'description' => $request->message,
                'status' => 'new',
            ]);

            if ($request->hasFile('files')) {
                $ticket->addMultipleMediaFromRequest(['files'])
                    ->each(fn($file) => $file->toMediaCollection('attachments', 'public'));
            }

            return new TicketResource($ticket);
        });
    }

    public function statistics()
    {
        $now = Carbon::now();

        $stats = [
            'today' => Ticket::whereDate('created_at', $now->toDateString())->count(),
            'week' => Ticket::whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
            'month' => Ticket::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
