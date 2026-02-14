<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        FileUpload::make('featured_image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('articles'),
                        RichEditor::make('content')
                            ->columnSpanFull()
                            ->extraAttributes([
                                'style' => 'min-height: 200px;',
                            ]),
                    ]),
                Section::make('Settings')
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'title')
                            ->searchable()
                            ->preload(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->rows(3),
                        DateTimePicker::make('published_at'),
                        Toggle::make('is_published')
                            ->default(false),
                    ])->collapsible(),
            ]);
    }
}
