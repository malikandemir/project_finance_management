<x-filament::page>
    <div class="space-y-6">
        @if($currentDocument)
            <x-filament::section>
                <x-slot name="headerEnd">
                    <x-filament::button
                        color="gray"
                        wire:click="backToCategories"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('help.back_to_categories') }}
                    </x-filament::button>
                </x-slot>
                
                <h1 class="text-2xl font-bold mb-4">
                    {{ $currentDocument->title }}
                </h1>
                
                <div class="prose dark:prose-invert max-w-none">
                    {!! $currentDocument->content !!}
                </div>
                
                @if($relatedDocuments->isNotEmpty())
                    <x-filament::section class="mt-6">
                        <x-slot name="heading">
                            {{ __('help.related_topics') }}
                        </x-slot>
                        
                        <x-filament::card>
                            <ul class="space-y-2">
                                @foreach($relatedDocuments as $document)
                                    <li>
                                        <x-filament::link 
                                            wire:click="viewDocument('{{ $document->slug }}')"
                                            tag="button"
                                        >
                                            {{ $document->title }}
                                        </x-filament::link>
                                    </li>
                                @endforeach
                            </ul>
                        </x-filament::card>
                    </x-filament::section>
                @endif
            </x-filament::section>
        @else
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('help.search') }}
                </x-slot>
                
                {{ $this->form }}
            </x-filament::section>
            
            @if($search)
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('help.search_results') }}
                    </x-slot>
                    
                    <x-slot name="headerEnd">
                        <x-filament::button
                            color="gray"
                            size="sm"
                            wire:click="clearSearch"
                        >
                            {{ __('help.clear_search') }}
                        </x-filament::button>
                    </x-slot>
                    
                    @if($documents->isEmpty())
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <div class="rounded-full bg-primary-50 p-3 dark:bg-primary-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-500 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                                </svg>
                            </div>
                            <h2 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                                {{ __('help.no_results') }}
                            </h2>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($documents as $document)
                                <x-filament::card wire:click="viewDocument('{{ $document->slug }}')" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <h3 class="text-lg font-medium">
                                        {{ $document->title }}
                                    </h3>
                                    
                                    <p class="text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">
                                        {!! Str::limit(strip_tags($document->content), 150) !!}
                                    </p>
                                </x-filament::card>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            @elseif(!$category)
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('help.categories') }}
                    </x-slot>
                    
                    @if($categories->isEmpty())
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <div class="rounded-full bg-primary-50 p-3 dark:bg-primary-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-500 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <h2 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                                {{ __('help.no_results') }}
                            </h2>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($categories as $cat)
                                <x-filament::card wire:click="selectCategory('{{ $cat }}')" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <h3 class="text-lg font-medium">
                                        {{ is_string($cat) ? $cat : (string)$cat }}
                                    </h3>
                                </x-filament::card>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            @else
                <x-filament::section>
                    <x-slot name="heading">
                        {{ $category }}
                    </x-slot>
                    
                    <x-slot name="headerEnd">
                        <x-filament::button
                            icon="heroicon-o-arrow-left"
                            color="gray"
                            wire:click="backToCategories"
                        >
                            {{ __('help.back_to_categories') }}
                        </x-filament::button>
                    </x-slot>
                    
                    @if($documents->isEmpty())
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <div class="rounded-full bg-primary-50 p-3 dark:bg-primary-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-500 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                                {{ __('help.no_results') }}
                            </h2>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($documents as $document)
                                <x-filament::card wire:click="viewDocument('{{ $document->slug }}')" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <h3 class="text-lg font-medium">
                                        {{ $document->title }}
                                    </h3>
                                    
                                    <p class="text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">
                                        {!! Str::limit(strip_tags($document->content), 150) !!}
                                    </p>
                                </x-filament::card>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            @endif
        @endif
    </div>
</x-filament::page>
