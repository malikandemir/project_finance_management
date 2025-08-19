<x-filament::page>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ \App\Filament\Resources\UserTypeResource::getUrl('index', ['user_type' => 'customers']) }}" 
               class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->query('user_type', 'customers') === 'customers' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <span class="flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    {{ __('Customers') }}
                </span>
            </a>
            <a href="{{ \App\Filament\Resources\UserTypeResource::getUrl('index', ['user_type' => 'suppliers']) }}" 
               class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->query('user_type') === 'suppliers' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <span class="flex items-center gap-2">
                    <x-heroicon-o-truck class="w-5 h-5" />
                    {{ __('Suppliers') }}
                </span>
            </a>
            <a href="{{ \App\Filament\Resources\UserTypeResource::getUrl('index', ['user_type' => 'employers']) }}" 
               class="px-4 py-2 text-sm font-medium rounded-lg {{ request()->query('user_type') === 'employers' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <span class="flex items-center gap-2">
                    <x-heroicon-o-briefcase class="w-5 h-5" />
                    {{ __('Employers') }}
                </span>
            </a>
        </div>
    </div>

    {{ $this->table }}
</x-filament::page>
