<?php

namespace App\Filament\Resources\HelpDocumentResource\Pages;

use App\Filament\Resources\HelpDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHelpDocument extends EditRecord
{
    protected static string $resource = HelpDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
