<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Просмотр заявки') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-xl font-bold mb-4">Тикет #{{ $ticket->id }}</h1>

        <div class="mb-4">
            <strong>Клиент:</strong> {{ $ticket->customer->name }} ({{ $ticket->customer->email }})
        </div>

        <div class="mb-4">
            <strong>Тема:</strong> {{ $ticket->subject }}
        </div>

        <div class="mb-4">
            <strong>Сообщение:</strong> {{ $ticket->description }}
        </div>

        <div class="mb-4">
            <strong>Файлы:</strong>
            @if($ticket->getMedia('attachments')->isEmpty())
            <p>Файлы не прикреплены.</p>
            @else
            <ul>
                @foreach($ticket->getMedia('attachments') as $file)
                <li class="mb-1">
                    @if(str_starts_with($file->mime_type, 'image/'))
                    <!-- Для изображений: можно открыть в новом окне или скачать -->
                    <a href="{{ $file->getUrl() }}" download="{{ $file->file_name }}">
                        <img src="{{ $file->getUrl() }}" alt="{{ $file->file_name }}" class="h-16 inline mr-2 rounded border">
                    </a>
                    <a href="{{ $file->getUrl() }}" class="link" download="{{ $file->file_name }}">
                        Скачать
                    </a>
                    @else
                    <!-- Для остальных файлов: скачать -->
                    <a href="{{ $file->getUrl() }}" download="{{ $file->file_name }}">
                        {{ $file->file_name }}
                    </a>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="mt-4">
            @csrf
            @method('PATCH')

            <label class="block mb-2 font-semibold">Статус:</label>
            <select name="status" class="border p-2 rounded">
                @foreach(['new','in_progress','completed'] as $status)
                <option value="{{ $status }}" {{ $ticket->status === $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="ml-2 bg-blue-600 text-white px-3 py-1 rounded">
                Сохранить
            </button>
        </form>

        @if(session('success'))
        <div class="mt-4 text-green-600">{{ session('success') }}</div>
        @endif
    </div>
</x-app-layout>