<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;

class TicketController extends Controller
{
    // Создание заявки
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

    // Получение статистики
    public function statistics()
    {
        $data = [
            'total' => Ticket::count(),
            'new' => Ticket::where('status', 'new')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'completed' => Ticket::where('status', 'completed')->count(),
        ];

        return response()->json($data);
    }
}
