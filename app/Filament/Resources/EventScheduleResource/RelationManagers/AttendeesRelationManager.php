<?php

namespace App\Filament\Resources\EventScheduleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttendeesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendees';

    protected static ?string $title = 'Peserta Terdaftar';

    protected static ?string $recordTitleAttribute = 'name';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->weight('medium'),
                Tables\Columns\TextColumn::make('phone')->label('Telepon')->placeholder('—'),
                Tables\Columns\TextColumn::make('order.order_number')->label('No. Pesanan')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('order.customer_name')->label('Pemesan')->placeholder('—')->toggleable(),
                Tables\Columns\ToggleColumn::make('present')->label('Hadir'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('present')
                    ->label('Kehadiran')
                    ->placeholder('Semua')
                    ->trueLabel('Hadir')
                    ->falseLabel('Belum hadir'),
            ])
            ->actions([
                Tables\Actions\Action::make('togglePresent')
                    ->label(fn ($record) => $record->present ? 'Tandai Tidak Hadir' : 'Tandai Hadir')
                    ->icon(fn ($record) => $record->present ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->present ? 'danger' : 'success')
                    ->action(fn ($record) => $record->update(['present' => ! $record->present])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markPresent')->label('Tandai Hadir')->icon('heroicon-o-check-circle')->color('success')
                    ->action(fn ($records) => $records->each->update(['present' => true]))->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('markAbsent')->label('Tandai Tidak Hadir')->icon('heroicon-o-x-circle')->color('danger')
                    ->action(fn ($records) => $records->each->update(['present' => false]))->deselectRecordsAfterCompletion(),
            ]);
    }
}
