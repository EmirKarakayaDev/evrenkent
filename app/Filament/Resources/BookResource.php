<?php

namespace App\Filament\Resources;

use App\Enums\ContentStatus;
use App\Filament\Concerns\RecordsContentReview;
use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookResource extends Resource
{
    use RecordsContentReview;

    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Yayın Yönetimi';

    protected static ?string $modelLabel = 'Kitap';

    protected static ?string $pluralModelLabel = 'Kitaplar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('author_id')
                    ->label('Yazar')
                    ->relationship('author', 'name')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $state, Forms\Set $set) => $set('slug', str($state)->slug())),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->label('Açıklama')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('Kapak Görseli')
                    ->image()
                    ->disk('public')
                    ->directory('covers/books')
                    ->maxSize(5120),
                Forms\Components\TextInput::make('price')
                    ->label('Fiyat')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('₺'),
                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options(collect(ContentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->default(ContentStatus::Taslak->value)
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation !== 'create')
                    ->dehydrated(fn (string $operation): bool => $operation === 'create')
                    ->helperText(fn (string $operation): ?string => $operation === 'create'
                        ? null
                        : 'Durum sadece aşağıdaki Onayla/Reddet/Yayınla aksiyonlarıyla değiştirilebilir.'),
                Forms\Components\Select::make('categories')
                    ->label('Kategoriler')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Yayın Tarihi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Yazar')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Kapak'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (ContentStatus $state) => $state->label()),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options(collect(ContentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Book $record): bool => auth()->user()->can('approve', $record))
                    ->action(function (Book $record): void {
                        abort_unless(auth()->user()->can('approve', $record), 403);

                        $record->update(['status' => ContentStatus::Onaylandi]);
                        static::recordReview($record, 'onaylandi');

                        Notification::make()->title('Kitap onaylandı')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Book $record): bool => auth()->user()->can('reject', $record))
                    ->form([
                        Forms\Components\Textarea::make('note')
                            ->label('Revizyon Notu')
                            ->required(),
                    ])
                    ->action(function (Book $record, array $data): void {
                        abort_unless(auth()->user()->can('reject', $record), 403);

                        $record->update(['status' => ContentStatus::RevizyonIstendi]);
                        static::recordReview($record, 'revizyon_istendi', $data['note']);

                        Notification::make()->title('Kitap revizyona gönderildi')->warning()->send();
                    }),

                Tables\Actions\Action::make('publish')
                    ->label('Yayınla')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Book $record): bool => auth()->user()->can('publish', $record))
                    ->action(function (Book $record): void {
                        abort_unless(auth()->user()->can('publish', $record), 403);

                        $record->update(['status' => ContentStatus::Yayinda, 'published_at' => now()]);
                        static::recordReview($record, 'yayinda');

                        Notification::make()->title('Kitap yayınlandı')->success()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
