<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Catatan aktivitas panel: siapa mengubah apa, kapan.
 *
 * Tidak memakai RestrictsToPermission seperti resource lain, karena akses ke
 * sini bukan soal izin konten melainkan soal peran. Admin konten yang bisa
 * membaca catatan ini bisa melihat seluruh perubahan admin lain — termasuk
 * perubahan yang justru sedang diaudit atas dirinya.
 */
class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Log Aktivitas';

    protected static ?string $modelLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Log Aktivitas';

    private static function isSuperAdmin(): bool
    {
        $user = auth()->user();

        return $user && method_exists($user, 'hasRole') && $user->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return self::isSuperAdmin();
    }

    public static function canViewAny(): bool
    {
        return self::isSuperAdmin();
    }

    public static function canView(Model $record): bool
    {
        return self::isSuperAdmin();
    }

    /* Catatan audit yang bisa dibuat, disunting, atau dihapus bukan catatan
       audit. Ketiganya ditutup rapat — termasuk bagi super admin, karena
       justru perbuatan super admin yang paling perlu tidak bisa dihapus
       sendiri. Pembersihan berkala, kalau nanti diperlukan, lewat perintah
       artisan yang jejaknya sendiri ada di riwayat peladen. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll(null)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y · H:i')
                    ->sortable()
                    ->description(fn (ActivityLog $r): string => $r->created_at?->diffForHumans() ?? ''),

                Tables\Columns\TextColumn::make('user_name')
                    ->label('Oleh')
                    ->searchable()
                    /* Dibaca dari salinan nama, bukan dari relasi: baris lama
                       harus tetap terbaca setelah akunnya dihapus — persis
                       keadaan ketika catatan audit paling dibutuhkan. */
                    ->description(fn (ActivityLog $r): ?string => $r->ip),

                Tables\Columns\TextColumn::make('event')
                    ->label('Tindakan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Menambah',
                        'updated' => 'Mengubah',
                        'deleted' => 'Menghapus',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Jenis Data')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('subject_label')
                    ->label('Data')
                    ->wrap()
                    ->limit(70)
                    ->searchable()
                    ->placeholder('—')
                    ->description(fn (ActivityLog $r): ?string => $r->subject_id ? '#'.$r->subject_id : null),

                /* Ringkasan perubahan dirangkai di kolomnya sendiri, bukan
                   disimpan jadi teks saat mencatat: bentuk tampilannya boleh
                   berubah tanpa menulis ulang baris yang sudah tersimpan. */
                Tables\Columns\TextColumn::make('changes')
                    ->label('Perubahan')
                    ->wrap()
                    ->placeholder('—')
                    ->formatStateUsing(function ($state): string {
                        if (! is_array($state) || ! $state) {
                            return '—';
                        }

                        $baris = [];
                        foreach (array_slice($state, 0, 4, true) as $kolom => $v) {
                            $baris[] = $kolom.': '.($v['dari'] ?? '—').' → '.($v['jadi'] ?? '—');
                        }

                        $sisa = count($state) - count($baris);

                        return implode("\n", $baris).($sisa > 0 ? "\n+{$sisa} kolom lain" : '');
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Tindakan')
                    ->options([
                        'created' => 'Menambah',
                        'updated' => 'Mengubah',
                        'deleted' => 'Menghapus',
                    ]),

                /* Pilihannya dibangun dari isi tabel log, bukan dari daftar
                   kelas yang ditulis tangan: jenis data baru muncul sendiri di
                   penyaring begitu ada yang pertama kali tercatat. */
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Jenis Data')
                    ->options(fn (): array => ActivityLog::query()
                        ->distinct()
                        ->orderBy('subject_type')
                        ->pluck('subject_type', 'subject_type')
                        ->map(fn (string $c): string => class_basename($c))
                        ->all()),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Admin')
                    ->options(fn (): array => User::orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('Belum ada aktivitas tercatat')
            ->emptyStateDescription('Setiap penambahan, perubahan, dan penghapusan data lewat panel akan muncul di sini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
