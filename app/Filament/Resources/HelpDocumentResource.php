<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HelpDocumentResource\Pages;
use App\Models\HelpDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class HelpDocumentResource extends Resource
{
    protected static ?string $model = HelpDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Help';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('help.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('help.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('language_code', app()->getLocale());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Locales')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('title.en')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                Forms\Components\RichEditor::make('content.en')
                                    ->label('Content')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Turkish')
                            ->schema([
                                Forms\Components\TextInput::make('title.tr')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('content.tr')
                                    ->label('Content')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('category')
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Document')
                    ->options(function (HelpDocument $record = null) {
                        return HelpDocument::query()
                            ->when($record, fn (Builder $query) => $query->where('id', '!=', $record->id))
                            ->pluck('title', 'id');
                    })
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(function () {
                        return HelpDocument::distinct()->pluck('category', 'category')->toArray();
                    }),
                Tables\Filters\SelectFilter::make('language_code')
                    ->options([
                        'en' => 'English',
                        'tr' => 'Turkish',
                    ])
                    ->default(app()->getLocale()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHelpDocuments::route('/'),
            'create' => Pages\CreateHelpDocument::route('/create'),
            'edit' => Pages\EditHelpDocument::route('/{record}/edit'),
        ];
    }
}
