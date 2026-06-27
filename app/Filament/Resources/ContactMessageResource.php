<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Pesan Kontak';

    protected static ?string $modelLabel = 'Pesan Kontak';

    protected static ?string $accessPermission = 'view_contact_messages';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('is_read', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\TextEntry::make('name')->label('Nama'),
            Infolists\Components\TextEntry::make('email')->copyable(),
            Infolists\Components\TextEntry::make('phone')->label('Telepon'),
            Infolists\Components\TextEntry::make('subject')->label('Subjek'),
            Infolists\Components\TextEntry::make('locale')->label('Bahasa')->badge(),
            Infolists\Components\TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Diterima'),
            Infolists\Components\TextEntry::make('message')->label('Pesan')->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\IconColumn::make('is_read')->label('Dibaca')->boolean(),
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('subject')->label('Subjek')->limit(40),
            Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Diterima')->sortable(),
        ])->filters([
            Tables\Filters\TernaryFilter::make('is_read')->label('Status Dibaca'),
        ])->actions([
            Tables\Actions\ViewAction::make()
                ->after(fn (ContactMessage $record) => $record->update(['is_read' => true])),
            Tables\Actions\Action::make('toggleRead')
                ->label(fn (ContactMessage $r) => $r->is_read ? 'Tandai Belum Dibaca' : 'Tandai Dibaca')
                ->icon('heroicon-o-check')
                ->action(fn (ContactMessage $r) => $r->update(['is_read' => ! $r->is_read])),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\BulkAction::make('markRead')->label('Tandai Dibaca')->icon('heroicon-o-check')
                    ->action(fn ($records) => $records->each->update(['is_read' => true])),
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
