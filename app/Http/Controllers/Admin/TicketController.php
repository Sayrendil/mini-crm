<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;


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

        // Сортировка
        if ($request->sort && in_array($request->sort, ['id', 'subject', 'status', 'created_at'])) {
            $direction = $request->direction === 'asc' ? 'asc' : 'desc';
            if ($request->sort === 'customer') {
                // сортировка по связанной модели
                $tickets->join('customers', 'tickets.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $direction)
                    ->select('tickets.*');
            } else {
                $tickets->orderBy($request->sort, $direction);
            }
        } else {
            $tickets->orderByDesc('created_at'); // дефолт
        }

        $tickets = $tickets->paginate(10)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    // Просмотр одного тикета
    public function show(Ticket $ticket)
    {
        $ticket->load('customer', 'media'); // media для прикреплённых файлов

        return view('tickets.show', compact('ticket'));
    }

    // Обновление статуса тикета (через форму)
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,completed',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Статус тикета обновлён');
    }
}
