<?php

namespace App\Filament\Resources\CertificateHolderResource\Pages;

use App\Filament\Resources\CertificateHolderResource;
use App\Models\CertificateHolder;
use App\Services\CertificateSpreadsheet;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

/**
 * Daftar sertifikat beserta tiga jalan masuk massal: unduh templat Excel,
 * unduh data yang sudah ada, dan unggah berkas yang sudah diisi. Ketiganya
 * memakai satu bentuk berkas (App\Services\CertificateSpreadsheet), sehingga
 * data yang diunduh bisa langsung disunting lalu diunggah kembali.
 */
class ListCertificateHolders extends ListRecords
{
    protected static string $resource = CertificateHolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('unduhTemplat')
                    ->label('Unduh templat Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn () => response()
                        ->download(app(CertificateSpreadsheet::class)->template(), 'templat-sertifikat.xlsx')
                        ->deleteFileAfterSend()),

                Actions\Action::make('unduhData')
                    ->label('Unduh data (Excel)')
                    ->icon('heroicon-o-table-cells')
                    ->action(fn () => response()
                        ->download(app(CertificateSpreadsheet::class)->export(), 'data-sertifikat-'.now()->format('Y-m-d').'.xlsx')
                        ->deleteFileAfterSend()),
            ])
                ->label('Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->button()
                ->color('gray'),

            Actions\Action::make('imporExcel')
                ->label('Impor dari Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Impor sertifikat dari Excel')
                ->modalDescription('Pakai berkas dari "Unduh templat Excel" atau "Unduh data". Nomor sertifikat yang sudah ada akan diperbarui, bukan digandakan. Bila ada satu baris yang salah, tidak ada data yang disimpan dan barisnya akan disebutkan.')
                ->modalSubmitActionLabel('Impor')
                ->form([
                    /* Disimpan ke disk lokal (bukan public) — berkas impor tidak
                       boleh bisa diunduh siapa pun lewat alamat /storage. Mime
                       application/zip ikut diterima karena sebagian peramban
                       dan sistem operasi melaporkan .xlsx sebagai arsip zip,
                       dan memang begitulah isinya; keabsahannya diperiksa saat
                       dibaca, bukan dari mime-nya. */
                    Forms\Components\FileUpload::make('berkas')
                        ->label('Berkas Excel (.xlsx)')
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/zip',
                            'application/octet-stream',
                        ])
                        ->maxSize(5120)
                        ->required()
                        ->helperText('Maksimal 5 MB dan '.number_format(CertificateSpreadsheet::MAKS_BARIS, 0, ',', '.').' baris.'),
                ])
                ->action(function (array $data): void {
                    $disk = Storage::disk('local');
                    $path = $disk->path($data['berkas']);

                    try {
                        $hasil = app(CertificateSpreadsheet::class)->import($path);
                    } finally {
                        // Berkas sementara selalu dibuang, berhasil atau tidak.
                        // @ karena di Windows berkas yang baru dibaca kadang
                        // masih terkunci sesaat; di peladen Linux tidak.
                        @unlink($path);
                    }

                    if (! $hasil['ok']) {
                        Notification::make()
                            ->title('Impor dibatalkan, tidak ada data yang disimpan')
                            ->body(implode("\n", $hasil['galat']))
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Impor selesai')
                        ->body("{$hasil['dibuat']} sertifikat baru ditambahkan, {$hasil['diperbarui']} diperbarui.")
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()->label('Tambah manual'),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Tambah satu per satu lewat "Tambah manual", atau banyak sekaligus lewat Excel. Status Berlaku, Segera berakhir, dan Kedaluwarsa di situs dihitung dari Tanggal Berakhir.';
    }
}
