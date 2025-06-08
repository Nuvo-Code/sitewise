<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;


class TemplateContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'templateContents';

    protected static ?string $title = 'Template Content';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('key')
                    ->label('Field')
                    ->options(function () {
                        $page = $this->getOwnerRecord();
                        if ($page->template) {
                            return collect($page->template->structure)
                                ->mapWithKeys(fn ($type, $key) => [$key => ucwords(str_replace('_', ' ', $key)) . " ({$type})"])
                                ->toArray();
                        }
                        return [];
                    })
                    ->required()
                    ->disabled(fn ($record) => $record !== null), // Disable editing key for existing records

                Forms\Components\Textarea::make('value')
                    ->label('Content')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('key')
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Field')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Content')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['template_id'] = $this->getOwnerRecord()->template_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No template content')
            ->emptyStateDescription('Add content fields for this template.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public function isReadOnly(): bool
    {
        return !$this->getOwnerRecord()->template_id;
    }
}
