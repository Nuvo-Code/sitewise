<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('site_id')
                    ->default(fn () => app('site')?->id),

                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Page::class, 'slug', ignoreRecord: true)
                            ->rules(['regex:/^[a-z0-9\-]+$/'])
                            ->helperText('Only lowercase letters, numbers, and hyphens allowed'),

                        Forms\Components\TextInput::make('title')
                            ->label('Page Title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('response_type')
                            ->label('Content Type')
                            ->options([
                                'html' => 'HTML',
                                'markdown' => 'Markdown',
                                'json' => 'JSON',
                            ])
                            ->required()
                            ->default('html')
                            ->live(),

                        Forms\Components\Select::make('template_id')
                            ->label('Template')
                            ->relationship('template', 'name')
                            ->options(fn () => Template::where('site_id', app('site')?->id)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->helperText('Optional: Select a template to use structured content'),

                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Content')
                    ->schema([
                        // Raw HTML Content
                        Forms\Components\Textarea::make('html_content')
                            ->label('HTML Content')
                            ->rows(10)
                            ->visible(fn (Forms\Get $get) =>
                                $get('response_type') === 'html' && !$get('template_id')
                            ),

                        // Markdown Content
                        Forms\Components\Textarea::make('markdown')
                            ->label('Markdown Content')
                            ->rows(10)
                            ->visible(fn (Forms\Get $get) =>
                                $get('response_type') === 'markdown' && !$get('template_id')
                            ),

                        // JSON Content
                        Forms\Components\Textarea::make('json_content')
                            ->label('JSON Content')
                            ->rows(10)
                            ->visible(fn (Forms\Get $get) =>
                                $get('response_type') === 'json' && !$get('template_id')
                            )
                            ->helperText('Enter valid JSON'),
                    ])
                    ->visible(fn (Forms\Get $get) => !$get('template_id')),

                Forms\Components\Section::make('Template Content')
                    ->schema([
                        Forms\Components\Placeholder::make('template_info')
                            ->label('')
                            ->content(fn (Forms\Get $get) =>
                                $get('template_id')
                                    ? 'Template fields will be available after saving the page with a template selected.'
                                    : 'Select a template above to use structured content.'
                            ),
                    ])
                    ->visible(fn (Forms\Get $get) => (bool) $get('template_id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('response_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'html' => 'primary',
                        'markdown' => 'success',
                        'json' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->default('—'),

                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('response_type')
                    ->options([
                        'html' => 'HTML',
                        'markdown' => 'Markdown',
                        'json' => 'JSON',
                    ]),
                Tables\Filters\TernaryFilter::make('active'),
                Tables\Filters\SelectFilter::make('template_id')
                    ->relationship('template', 'name')
                    ->label('Template'),
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
            ->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('site_id', app('site')?->id);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\PageResource\RelationManagers\TemplateContentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
