<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Заявки') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-xl font-bold mb-4">Все заявки</h1>

        <table class="w-full border border-gray-200 rounded">
            <thead class="bg-gray-100">
                <tr>
                    <!-- ID с сортировкой -->
                    <th class="p-2 border-b">
                        <div class="flex items-center gap-1">
                            <span>ID</span>
                            <div class="flex flex-col">
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'id','direction'=>'asc'])) }}">▲</a>
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'id','direction'=>'desc'])) }}">▼</a>
                            </div>
                        </div>
                        <input type="text" name="id" value="{{ request('id') }}"
                            class="mt-1 border rounded px-1 py-0.5 text-sm w-16"
                            onchange="filterTable()">
                    </th>

                    <!-- Клиент -->
                    <th class="p-2 border-b">
                        <div class="flex items-center gap-1">
                            <span>Клиент</span>
                            <div class="flex flex-col">
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'customer','direction'=>'asc'])) }}">▲</a>
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'customer','direction'=>'desc'])) }}">▼</a>
                            </div>
                        </div>
                        <input type="text" name="customer" value="{{ request('customer') }}"
                            class="mt-1 border rounded px-1 py-0.5 text-sm w-32"
                            onchange="filterTable()">
                    </th>

                    <!-- Тема -->
                    <th class="p-2 border-b">
                        <div class="flex items-center gap-1">
                            <span>Тема</span>
                            <div class="flex flex-col">
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'subject','direction'=>'asc'])) }}">▲</a>
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'subject','direction'=>'desc'])) }}">▼</a>
                            </div>
                        </div>
                        <input type="text" name="subject" value="{{ request('subject') }}"
                            class="mt-1 border rounded px-1 py-0.5 text-sm w-32"
                            onchange="filterTable()">
                    </th>

                    <!-- Статус -->
                    <th class="p-2 border-b">
                        <div class="flex items-center gap-1">
                            <span>Статус</span>
                            <div class="flex flex-col">
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'status','direction'=>'asc'])) }}">▲</a>
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'status','direction'=>'desc'])) }}">▼</a>
                            </div>
                        </div>
                        <select name="status" class="mt-1 border rounded px-1 py-0.5 text-sm w-28"
                            onchange="filterTable()">
                            <option value="">Все</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </th>

                    <!-- Дата -->
                    <th class="p-2 border-b">
                        <div class="flex items-center gap-1">
                            <span>Дата</span>
                            <div class="flex flex-col">
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'created_at','direction'=>'asc'])) }}">▲</a>
                                <a href="{{ route('tickets.index', array_merge(request()->all(), ['sort'=>'created_at','direction'=>'desc'])) }}">▼</a>
                            </div>
                        </div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="mt-1 border rounded px-1 py-0.5 text-sm w-32"
                            onchange="filterTable()">
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="mt-1 border rounded px-1 py-0.5 text-sm w-32"
                            onchange="filterTable()">
                    </th>

                    <th class="p-2 border-b">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td class="p-2 border-b">{{ $ticket->id }}</td>
                    <td class="p-2 border-b">{{ $ticket->customer->name }}</td>
                    <td class="p-2 border-b">{{ $ticket->subject }}</td>
                    <td class="p-2 border-b">{{ $ticket->status }}</td>
                    <td class="p-2 border-b">{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                    <td class="p-2 border-b">
                        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline">Просмотр</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-2 text-center text-gray-500">Заявки не найдены</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
    </div>

    <script>
        function filterTable() {
            const params = new URLSearchParams(window.location.search);
            document.querySelectorAll('thead input, thead select').forEach(el => {
                if (el.value) {
                    params.set(el.name, el.value);
                } else {
                    params.delete(el.name);
                }
            });
            window.location.search = params.toString();
        }
    </script>
</x-app-layout>