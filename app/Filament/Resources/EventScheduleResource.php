<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\EventScheduleResource\Pages;
use App\Models\ServiceSchedule;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventScheduleResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = ServiceSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Acara & Peserta';

    protected static ?string $modelLabel = 'Acara';

    protected static ?string $pluralModelLabel = 'Acara';

    protected static ?int $navigationSort = 3;

    protected static ?string $accessPermission = 'view_orders';

    protected static function timeframeColor(string $state): string
    {
        return match ($state) {
            'upcoming' => 'info',
            'ongoing' => 'success',
            default => 'gray',
        };
    }

    protected static function timeframeLabel(string $state): string
    {
        return match ($state) {
            'upcoming' => 'Mendatang',
            'ongoing' => 'Berlangsung',
            default => 'Selesai',
        };
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('service')
            ->withSum(['orders as paid_seats' => fn ($q) => $q->where('status', 'paid')], 'quantity')
            ->withCount(['orders as paid_orders_count' => fn ($q) => $q->where('status', 'paid')])
            ->withCount([
                'attendees as attendees_count',
                'attendees as present_count' => fn ($q) => $q->where('order_participants.present', true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('service.title')->label('Layanan')->limit(34)->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('start_date')->label('Mulai')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('end_date')->label('Selesai')->date('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('mode')->label('Mode')->badge(),
                Tables\Columns\TextColumn::make('location')->label('Lokasi')->limit(24)->placeholder('—'),
                Tables\Columns\TextColumn::make('timeframe')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => static::timeframeLabel($state))
                    ->color(fn (string $state) => static::timeframeColor($state)),
                Tables\Columns\TextColumn::make('present_count')
                    ->label('Hadir')
                    ->badge()
                    ->color(fn (ServiceSchedule $record) => $record->present_count >= $record->attendees_count && $record->attendees_count > 0 ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state, ServiceSchedule $record) => (int) $state.' / '.(int) $record->attendees_count),
                Tables\Columns\TextColumn::make('quota')
                    ->label('Kuota / Sisa')
                    ->placeholder('∞')
                    ->formatStateUsing(fn ($state, ServiceSchedule $record) => $state === null ? '∞' : $state.' / '.$record->seats_available),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('timeframe')
                    ->label('Waktu')
                    ->options([
                        'active' => 'Mendatang & Berlangsung',
                        'upcoming' => 'Mendatang',
                        'ongoing' => 'Berlangsung',
                        'past' => 'Selesai',
                    ])
                    ->default('active')
                    ->query(function (Builder $query, array $data): Builder {
                        $today = today()->toDateString();

                        return match ($data['value'] ?? null) {
                            'upcoming' => $query->whereDate('start_date', '>', $today),
                            'ongoing' => $query->whereDate('start_date', '<=', $today)
                                ->whereRaw('COALESCE(end_date, start_date) >= ?', [$today]),
                            'past' => $query->whereRaw('COALESCE(end_date, start_date) < ?', [$today]),
                            'active' => $query->whereRaw('COALESCE(end_date, start_date) >= ?', [$today]),
                            default => $query,
                        };
                    }),
                Tables\Filters\SelectFilter::make('service')
                    ->label('Layanan')
                    ->relationship('service', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([Tables\Actions\ViewAction::make()->label('Peserta')]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Jadwal Acara')->schema([
                Infolists\Components\TextEntry::make('service.title')->label('Layanan')->placeholder('—'),
                Infolists\Components\TextEntry::make('timeframe')->label('Status')->badge()
                    ->formatStateUsing(fn (string $state) => static::timeframeLabel($state))
                    ->color(fn (string $state) => static::timeframeColor($state)),
                Infolists\Components\TextEntry::make('start_date')->label('Tanggal Mulai')->date('d M Y'),
                Infolists\Components\TextEntry::make('end_date')->label('Tanggal Selesai')->date('d M Y')->placeholder('—'),
                Infolists\Components\TextEntry::make('start_time')->label('Jam Mulai')->placeholder('—'),
                Infolists\Components\TextEntry::make('end_time')->label('Jam Selesai')->placeholder('—'),
                Infolists\Components\TextEntry::make('mode')->label('Mode')->badge(),
                Infolists\Components\TextEntry::make('location')->label('Lokasi')->placeholder('—'),
                Infolists\Components\TextEntry::make('seats_taken')->label('Kursi Terisi'),
                Infolists\Components\TextEntry::make('quota')->label('Kuota')->placeholder('Tanpa batas'),
            ])->columns(3),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            EventScheduleResource\RelationManagers\AttendeesRelationManager::class,
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventSchedules::route('/'),
            'view' => Pages\ViewEventSchedule::route('/{record}'),
        ];
    }
}
