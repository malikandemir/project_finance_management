<?php

namespace App\Filament\Resources\HelpDocumentResource\Pages;

use App\Filament\Resources\HelpDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHelpDocuments extends ListRecords
{
    protected static string $resource = HelpDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
