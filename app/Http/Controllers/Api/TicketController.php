<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
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
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection('attachments');
                    });
            }

            return new TicketResource($ticket);
        });
    }
}
