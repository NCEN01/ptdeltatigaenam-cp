<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Tanya Jawab (FAQ)';

    protected static ?string $modelLabel = 'Tanya Jawab';

    protected static ?string $pluralModelLabel = 'Tanya Jawab';

    protected static ?int $navigationSort = 130;

    protected static ?string $accessPermission = 'manage_faqs';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Pertanyaan & Jawaban')
                ->description('Tampil di seksi Tanya Jawab pada beranda. Versi Inggris boleh dikosongkan — bila kosong, versi Indonesia yang dipakai.')
                ->schema([
                    Forms\Components\Textarea::make('question.id')
                        ->label('Pertanyaan (ID)')
                        ->required()
                        ->rows(2)
                        /* Batasnya diikat ke tampilan, bukan ke lebar kolom basis
                           data. Pertanyaan tampil sebagai satu baris yang boleh
                           turun dua baris di kartunya; melewati sekitar 180
                           karakter ia menjadi tiga baris dan nomor urutnya tidak
                           lagi rata dengan barisnya. */
                        ->maxLength(180)
                        ->helperText('Maksimal 180 karakter agar tetap muat dua baris di kartunya.'),
                    Forms\Components\Textarea::make('question.en')
                        ->label('Pertanyaan (EN)')
                        ->rows(2)
                        ->maxLength(180),
                    Forms\Components\Textarea::make('answer.id')
                        ->label('Jawaban (ID)')
                        ->required()
                        ->rows(5)
                        ->maxLength(1200),
                    Forms\Components\Textarea::make('answer.en')
                        ->label('Jawaban (EN)')
                        ->rows(5)
                        ->maxLength(1200),
                ])->columns(2),

            Forms\Components\Section::make('Urutan & Status')
                ->schema([
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0)
                        ->helperText('Angka kecil tampil lebih dulu.'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->helperText('Dinonaktifkan, pertanyaan ini tidak tampil di beranda tetapi datanya tersimpan.'),
                ])->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            /* Bisa diurut dengan seret. Dengan hanya enam baris, mengatur
               urutannya lewat kolom angka berarti membuka enam formulir untuk
               satu pekerjaan yang sebenarnya satu gerakan. */
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->searchable()
                    ->limit(120),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada tanya jawab')
            ->emptyStateDescription('Selama tabel ini kosong, beranda menampilkan enam tanya jawab bawaan. Isi di sini untuk menggantinya.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
