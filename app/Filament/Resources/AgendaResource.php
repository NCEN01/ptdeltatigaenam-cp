<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\AgendaResource\Pages;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AgendaResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Agenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 140;

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $modelLabel = 'Agenda';

    protected static ?string $accessPermission = 'manage_agenda';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Agenda (ID/EN)')->schema([
                Forms\Components\TextInput::make('title.id')
                    ->label('Judul (ID)')
                    ->required()
                    ->maxLength(280)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                Forms\Components\TextInput::make('title.en')
                    ->label('Judul (EN)')
                    ->maxLength(280),
                Forms\Components\TextInput::make('slug')->required()->maxLength(280)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('location')->label('Lokasi')->maxLength(200),
                Forms\Components\Textarea::make('excerpt.id')->label('Ringkasan (ID)')->rows(2),
                Forms\Components\Textarea::make('excerpt.en')->label('Ringkasan (EN)')->rows(2),
                Forms\Components\RichEditor::make('content.id')->label('Deskripsi (ID)')->columnSpanFull(),
                Forms\Components\RichEditor::make('content.en')->label('Deskripsi (EN)')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Jadwal & Media')->schema([
                Forms\Components\DateTimePicker::make('starts_at')->label('Waktu Mulai')->required(),
                Forms\Components\DateTimePicker::make('ends_at')->label('Waktu Selesai'),
                MediaUpload::for('image', 'blog', 'agenda')->label('Gambar'),
                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft', 'published' => 'Terbit', 'archived' => 'Arsip',
                ])->default('draft')->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('starts_at', 'desc')->columns([
            Tables\Columns\ImageColumn::make('image')->disk('public')->label('')->size(60),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('location')->label('Lokasi')->toggleable(),
            Tables\Columns\TextColumn::make('starts_at')->dateTime('d M Y H:i')->label('Mulai')->sortable(),
            Tables\Columns\TextColumn::make('status')->badge()->colors([
                'gray' => 'draft', 'success' => 'published', 'warning' => 'archived',
            ]),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Terbit', 'archived' => 'Arsip']),
        ])->headerActions([
            Tables\Actions\Action::make('syncAutoDraft')
                ->label('Auto-Draft Agenda < 1 Minggu')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Konversi Agenda Terdekat ke Draft Layanan')
                ->modalDescription('Seluruh agenda berstatus Terbit yang waktu mulainya tinggal <= 7 hari ke depan akan otomatis dibuatkan Draft Layanan untuk direview.')
                ->action(function (): void {
                    $count = Agenda::syncUpcomingAgendasToDraftServices();
                    Notification::make()
                        ->title($count > 0 ? "{$count} draft layanan berhasil dibuat" : 'Tidak ada agenda baru yang perlu dikonversi')
                        ->success()
                        ->send();
                }),
        ])->actions([
            Tables\Actions\Action::make('draftToService')
                ->label('Jadikan Draft Layanan')
                ->icon('heroicon-o-document-duplicate')
                ->color('gold')
                ->requiresConfirmation()
                ->modalHeading('Buat Draft Layanan dari Agenda Ini')
                ->modalDescription('Agenda ini akan disalin menjadi layanan berstatus Draft (nonaktif) beserta jadwalnya, dan notifikasi review akan dikirim ke Admin Konten & Super Admin.')
                ->visible(fn (Agenda $record): bool => $record->draftedService() === null)
                ->action(function (Agenda $record): void {
                    $service = $record->draftToService();
                    if ($service) {
                        Notification::make()
                            ->title('Draft layanan berhasil dibuat')
                            ->body('Silakan lengkapi kategori serta harga di menu Layanan.')
                            ->success()
                            ->send();
                    }
                }),
            Tables\Actions\Action::make('viewDraftService')
                ->label('Review Layanan')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->visible(fn (Agenda $record): bool => $record->draftedService() !== null)
                ->url(fn (Agenda $record): ?string => ($svc = $record->draftedService())
                    ? ServiceResource::getUrl('edit', ['record' => $svc->getKey()])
                    : null),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }
}