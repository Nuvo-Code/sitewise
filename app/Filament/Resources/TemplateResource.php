<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('site_id')
                    ->default(fn () => app('site')?->id),

                Forms\Components\Section::make('Template Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Template Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Template::class, 'name', ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->helperText('Optional description of what this template is for'),

                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Template Structure')
                    ->schema([
                        Forms\Components\Repeater::make('structure')
                            ->label('Template Fields')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Field Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('key', str_replace([' ', '-'], '_', strtolower($state)));
                                    }),

                                Forms\Components\Hidden::make('key'),

                                Forms\Components\Select::make('type')
                                    ->label('Field Type')
                                    ->required()
                                    ->options([
                                        'text' => 'Text Input',
                                        'textarea' => 'Textarea',
                                        'rich_text' => 'Rich Text Editor',
                                        'number' => 'Number',
                                        'email' => 'Email',
                                        'url' => 'URL',
                                        'date' => 'Date',
                                        'datetime' => 'Date & Time',
                                        'select' => 'Select Dropdown',
                                        'checkbox' => 'Checkbox',
                                        'toggle' => 'Toggle Switch',
                                        'file' => 'File Upload',
                                        'image' => 'Image Upload',
                                        'color' => 'Color Picker',
                                    ])
                                    ->live(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Field Description')
                                    ->rows(2)
                                    ->helperText('Optional description for content editors'),

                                Forms\Components\Group::make([
                                    Forms\Components\Toggle::make('required')
                                        ->label('Required Field')
                                        ->default(false),

                                    Forms\Components\TextInput::make('default_value')
                                        ->label('Default Value')
                                        ->helperText('Optional default value for this field'),
                                ])
                                ->columns(2),

                                // Options for select fields
                                Forms\Components\KeyValue::make('options')
                                    ->label('Select Options')
                                    ->keyLabel('Value')
                                    ->valueLabel('Label')
                                    ->addActionLabel('Add Option')
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'select')
                                    ->helperText('Define the available options for this select field'),

                                // Validation rules
                                Forms\Components\TagsInput::make('validation_rules')
                                    ->label('Validation Rules')
                                    ->helperText('Laravel validation rules (e.g., min:3, max:255)')
                                    ->placeholder('Add validation rule'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Field')
                            ->addActionLabel('Add Template Field')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->default([
                                [
                                    'name' => 'Title',
                                    'key' => 'title',
                                    'type' => 'text',
                                    'required' => true,
                                    'description' => 'The main title for this content',
                                ],
                                [
                                    'name' => 'Content',
                                    'key' => 'content',
                                    'type' => 'rich_text',
                                    'required' => true,
                                    'description' => 'The main content body',
                                ],
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->default('—'),

                Tables\Columns\TextColumn::make('pages_count')
                    ->label('Pages Using')
                    ->counts('pages'),

                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active'),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('site_id', app('site')?->id)
            ->withCount('pages');
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
