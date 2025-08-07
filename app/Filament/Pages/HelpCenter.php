<?php

namespace App\Filament\Pages;

use App\Models\HelpDocument;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Url;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Components\ViewComponent;
use Filament\Support\View\Components\Tabs;

class HelpCenter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Help';

    protected static ?int $navigationSort = 40;
    
    protected static ?string $title = null;
    
    protected static string $view = 'filament.pages.help-center';
    
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    #[Url(as: 'q')]
    public ?string $search = null;

    #[Url(as: 'cat')]
    public ?string $category = null;

    #[Url(as: 'doc')]
    public ?string $documentSlug = null;

    public ?HelpDocument $currentDocument = null;

    public function mount(): void
    {
        if ($this->documentSlug) {
            $locale = App::getLocale();
            $this->currentDocument = HelpDocument::where('slug', $this->documentSlug)
                ->where('language_code', $locale)
                ->first();
        }
    }

    public function getTitle(): string|Htmlable
    {
        return __('help.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('help.title');
    }

    public function getCategories(): Collection
    {
        $locale = App::getLocale();
        
        return HelpDocument::select('category')
            ->whereNotNull('category')
            ->where('language_code', $locale)
            ->distinct()
            ->pluck('category');
    }
    
    protected function getViewData(): array
    {
        return [
            'categories' => $this->getCategories(),
        ];
    }

    public function getDocuments(): Collection
    {
        $locale = App::getLocale();
        
        $query = HelpDocument::query()
            ->where('language_code', $locale)
            ->when($this->category, fn (Builder $query) => $query->where('category', $this->category))
            ->when($this->search, function (Builder $query) {
                return $query->where(function (Builder $query) {
                    $query->where('title', 'like', "%{$this->search}%")
                        ->orWhere('content', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('category')
            ->orderBy('order');

        return $query->get();
    }

    public function getRelatedDocuments(): Collection
    {
        if (!$this->currentDocument) {
            return collect();
        }
        
        $locale = App::getLocale();

        return HelpDocument::where('category', $this->currentDocument->category)
            ->where('id', '!=', $this->currentDocument->id)
            ->where('language_code', $locale)
            ->limit(5)
            ->get();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('search')
                    ->placeholder(__('help.search_placeholder'))
                    ->hiddenLabel(),
            ]);
    }

    public function viewDocument(string $slug): void
    {
        $locale = App::getLocale();
        $this->documentSlug = $slug;
        $this->currentDocument = HelpDocument::where('slug', $slug)
            ->where('language_code', $locale)
            ->first();
    }

    public function clearSearch(): void
    {
        $this->search = null;
    }

    public function selectCategory(?string $category): void
    {
        $this->category = $category;
        $this->documentSlug = null;
        $this->currentDocument = null;
    }

    public function backToCategories(): void
    {
        $this->category = null;
        $this->documentSlug = null;
        $this->currentDocument = null;
    }
}
