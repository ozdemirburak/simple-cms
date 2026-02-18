<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Models\MediaItem;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')
                ->label('Upload Images')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    TextInput::make('name')
                        ->label('Name (optional)')
                        ->placeholder('e.g., Blog header images')
                        ->maxLength(255),
                    FileUpload::make('images')
                        ->label('Select Images')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->imageEditor()
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                        ->required()
                        ->helperText('Max 5MB per file. Supported: JPG, PNG, GIF, WebP'),
                ])
                ->action(function (array $data): void {
                    $mediaItem = MediaItem::create([
                        'name' => $data['name'] ?? 'Uploaded ' . now()->format('Y-m-d H:i'),
                    ]);

                    foreach ($data['images'] as $image) {
                        $mediaItem->addMedia(storage_path('app/private/livewire-tmp/' . $image))
                            ->toMediaCollection('images');
                    }

                    $count = count($data['images']);

                    Notification::make()
                        ->title("Uploaded {$count} image(s) successfully")
                        ->success()
                        ->send();
                }),
        ];
    }
}
