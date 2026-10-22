<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\ManagesSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

/**
 * Ajakan "Konsultasi Program Gratis" di bagian bawah setiap halaman program.
 * Satu teks untuk semua program. Semula bagian dari "Pengaturan Situs".
 */
class ManageProgramConsult extends Page implements HasForms
{
    use InteractsWithForms, ManagesSettings;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?int $navigationSort = 45;

    protected static ?string $navigationLabel = 'Konsultasi Program';

    protected static ?string $title = 'Konsultasi Program';

    protected static string $view = 'filament.pages.manage-settings';

    protected function settingTypes(): array
    {
        return [
            'program_consult_title' => ['json', 'services'],
            'program_consult_desc' => ['json', 'services'],
            'program_consult_cta' => ['json', 'services'],
            'program_consult_whatsapp' => ['text', 'services'],
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Konsultasi Program Gratis')
                ->description('Tampil di bagian bawah setiap halaman program. Mengubahnya sekali berlaku untuk semua program.')
                ->schema([
                    TextInput::make('program_consult_title.id')->label('Judul (ID)')->maxLength(160)
                        ->placeholder('Konsultasi Program Gratis')
                        ->helperText('Kosongkan untuk memakai teks bawaan.'),
                    TextInput::make('program_consult_title.en')->label('Judul (EN)')->maxLength(160),
                    Textarea::make('program_consult_desc.id')->label('Keterangan (ID)')->maxLength(600)->rows(2)
                        ->placeholder('Punya pertanyaan dan ingin berbicara langsung dengan tim kami?'),
                    Textarea::make('program_consult_desc.en')->label('Keterangan (EN)')->maxLength(600)->rows(2),
                    TextInput::make('program_consult_cta.id')->label('Teks Tombol (ID)')->maxLength(60)
                        ->placeholder('Konsultasi Sekarang'),
                    TextInput::make('program_consult_cta.en')->label('Teks Tombol (EN)')->maxLength(60),
                    TextInput::make('program_consult_whatsapp')->label('Nomor WhatsApp')->maxLength(30)
                        ->placeholder('62818834766')
                        ->helperText('Diawali 62, tanpa tanda plus dan tanpa spasi. Dikosongkan berarti tombolnya menuju halaman Kontak.'),
                ])->columns(2),
        ])->statePath('data');
    }
}
