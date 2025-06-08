<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $modelLabel = 'Site Settings';

    protected static ?string $pluralModelLabel = 'Site Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Site Information')
                    ->description('Basic information about your site')
                    ->schema([
                        Forms\Components\TextInput::make('domain')
                            ->label('Domain')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('This domain is automatically detected and cannot be changed'),

                        Forms\Components\TextInput::make('name')
                            ->label('Site Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('A friendly name for your site'),

                        Forms\Components\Textarea::make('settings.description')
                            ->label('Description')
                            ->rows(3)
                            ->helperText('Optional description of your site'),

                        Forms\Components\Toggle::make('active')
                            ->label('Site Active')
                            ->helperText('Disable to temporarily take the site offline')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Not used since we only show the edit form
        return $table
            ->columns([])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', app('site')?->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditSite::route('/'),
        ];
    }
}
