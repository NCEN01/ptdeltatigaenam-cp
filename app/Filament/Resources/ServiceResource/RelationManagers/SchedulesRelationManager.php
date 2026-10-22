<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Models\ServiceSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Batch (angkatan) sebuah layanan, dikelola terpisah dari form layanannya.
 *
 * Tampil sebagai tabel di bawah halaman sunting layanan, dengan tombol "Tambah
 * Batch" sendiri. Layanan tetap sah tanpa batch sama sekali — konsultasi atau
 * headhunter yang dikerjakan per penugasan memang tidak punya tanggal.
 *
 * Isian sebuah batch sengaja sedikit: kapan dan berapa kursi. Lokasi, cara
 * mengikuti, dan harga semuanya dibaca dari layanannya; dulu ketiganya diisi
 * ulang di setiap jadwal dan tidak pernah berbeda dari yang di layanan.
 *
 * Kursi terisi TIDAK bisa diketik. Ia bertambah sendiri lewat Order::booted()
 * setiap kali pesanan untuk batch ini lunas, dan berkurang bila pesanan lunas
 * itu dibatalkan. Isian manual dulu bisa membuat angkanya tidak cocok dengan
 * pesanan yang sebenarnya ada.
 */
class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Batch & Jadwal';

    protected static ?string $modelLabel = 'Batch';

    protected static ?string $pluralModelLabel = 'Batch';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Tanggal & Jam')
                ->schema([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->live(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->afterOrEqual('start_date')
                        ->validationMessages(['after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.'])
                        ->helperText('Kosongkan bila hanya satu hari.'),
                    Forms\Components\TimePicker::make('start_time')->label('Jam Mulai')->seconds(false),
                    Forms\Components\TimePicker::make('end_time')->label('Jam Selesai')->seconds(false),
                ])->columns(2),

            Forms\Components\Section::make('Kursi')
                ->schema([
                    /* Kuota tidak boleh diturunkan di bawah kursi yang sudah
                       terjual. Tanpa batas ini, sisa kursi menjadi negatif dan
                       halaman program menampilkan batch yang sudah "lebih dari
                       penuh" sambil tetap menerima pendaftaran baru. */
                    Forms\Components\TextInput::make('quota')
                        ->label('Kuota Peserta')
                        ->numeric()
                        ->integer()
                        ->minValue(fn (?ServiceSchedule $record): int => max(1, (int) ($record?->seats_taken ?? 0)))
                        ->validationMessages(['min' => 'Kuota tidak boleh lebih kecil dari kursi yang sudah terisi.'])
                        ->helperText('Kosongkan bila batch ini tidak dibatasi.'),
                    Forms\Components\Placeholder::make('seats_info')
                        ->label('Kursi Terisi')
                        ->content(fn (?ServiceSchedule $record): string => $record
                            ? $record->seats_taken.' kursi — bertambah otomatis dari pesanan yang lunas'
                            : '0 kursi — bertambah otomatis dari pesanan yang lunas'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Tampilkan di situs')
                        ->default(true)
                        ->helperText('Dimatikan, batch ini tidak tampil dan tidak bisa dibeli, tetapi pesanannya tetap tersimpan.')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn (ServiceSchedule $record): string => 'Batch '.($record->start_date?->translatedFormat('d M Y') ?? ''))
            ->defaultSort('start_date')
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal')
                    ->sortable()
                    ->formatStateUsing(function (ServiceSchedule $record): string {
                        $mulai = $record->start_date?->translatedFormat('d M Y');
                        $selesai = $record->end_date && ! $record->end_date->isSameDay($record->start_date)
                            ? $record->end_date->translatedFormat('d M Y')
                            : null;

                        return $selesai ? "{$mulai} – {$selesai}" : (string) $mulai;
                    }),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam')
                    ->formatStateUsing(fn (ServiceSchedule $record): string => $record->start_time
                        ? Carbon::parse($record->start_time)->format('H:i').($record->end_time ? '–'.Carbon::parse($record->end_time)->format('H:i') : '')
                        : '-'),
                Tables\Columns\TextColumn::make('quota')->label('Kuota')->placeholder('Tak dibatasi'),
                Tables\Columns\TextColumn::make('seats_taken')->label('Terisi'),
                Tables\Columns\TextColumn::make('seats_available')
                    ->label('Sisa')
                    ->state(fn (ServiceSchedule $record): string => $record->seats_available === null ? '-' : (string) $record->seats_available)
                    ->badge()
                    ->color(fn (ServiceSchedule $record): string => match (true) {
                        $record->seats_available === null => 'gray',
                        $record->seats_available === 0 => 'danger',
                        $record->seats_available <= 5 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('timeframe')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'upcoming' => 'Akan datang',
                        'ongoing' => 'Berlangsung',
                        default => 'Selesai',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'upcoming' => 'info',
                        'ongoing' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\ToggleColumn::make('is_active')->label('Tampil'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Batch'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                /* Batch yang sudah punya pesanan tidak boleh dihapus: pesanan
                   menunjuknya lewat service_schedule_id, dan menghapusnya
                   menghapus jejak siapa ikut angkatan mana. Admin diarahkan
                   untuk menyembunyikannya saja. */
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (ServiceSchedule $record): bool => ! $record->orders()->exists()),
            ])
            ->emptyStateHeading('Belum ada batch')
            ->emptyStateDescription('Layanan ini belum punya angkatan bertanggal. Tambahkan batch bila program dibuka pada tanggal tertentu; layanan tetap tampil tanpa batch.');
    }

    /** Batch yang sudah punya pesanan hanya bisa disembunyikan, tidak dihapus. */
    protected function canDelete(Model $record): bool
    {
        return ! $record->orders()->exists();
    }
}
