<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Статистика') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-bold mb-4">Статистика заявок</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="p-4 bg-blue-100 rounded text-center">
                            <div class="text-2xl font-semibold">{{ $stats['today'] }}</div>
                            <div>За сегодня</div>
                        </div>
                        <div class="p-4 bg-green-100 rounded text-center">
                            <div class="text-2xl font-semibold">{{ $stats['week'] }}</div>
                            <div>За неделю</div>
                        </div>
                        <div class="p-4 bg-yellow-100 rounded text-center">
                            <div class="text-2xl font-semibold">{{ $stats['month'] }}</div>
                            <div>За месяц</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>