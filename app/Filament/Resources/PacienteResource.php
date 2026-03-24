<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacienteResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PacienteResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Clinica';

    protected static ?string $navigationLabel = 'Pacientes';

    public static function form(Form $form): Form
    {
        $canEditPatient = auth()->user()?->isAdmin() || auth()->user()?->isAsistente();

        return $form
            ->schema([
                Forms\Components\Section::make('Paciente')
                    ->schema([
                        Forms\Components\Hidden::make('role')
                            ->default(User::ROLE_PACIENTE),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->disabled(! $canEditPatient),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(! $canEditPatient),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Expediente clinico')
                    ->relationship('expedienteClinico')
                    ->schema([
                        Forms\Components\TextInput::make('telefono')
                            ->tel()
                            ->maxLength(30)
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                        Forms\Components\TextInput::make('direccion')
                            ->maxLength(255)
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                        Forms\Components\DatePicker::make('fecha_nacimiento')
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                        Forms\Components\TextInput::make('tipo_sangre')
                            ->maxLength(5)
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                        Forms\Components\Textarea::make('alergias')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                        Forms\Components\Textarea::make('antecedentes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                        Forms\Components\Textarea::make('medicamentos_actuales')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                        Forms\Components\Textarea::make('notas')
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled(fn (): bool => auth()->user()?->isAsistente() ?? false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Paciente')
                    ->schema([
                        TextEntry::make('name')->label('Nombre'),
                        TextEntry::make('email'),
                    ])
                    ->columns(2),
                InfolistSection::make('Expediente clinico')
                    ->schema([
                        TextEntry::make('expedienteClinico.telefono')->label('Telefono')->placeholder('Sin registro'),
                        TextEntry::make('expedienteClinico.direccion')->label('Direccion')->placeholder('Sin registro'),
                        TextEntry::make('expedienteClinico.fecha_nacimiento')->date()->label('Nacimiento'),
                        TextEntry::make('expedienteClinico.tipo_sangre')->label('Tipo de sangre')->placeholder('Sin registro'),
                        TextEntry::make('expedienteClinico.alergias')->placeholder('Sin registro')->columnSpanFull(),
                        TextEntry::make('expedienteClinico.antecedentes')->placeholder('Sin registro')->columnSpanFull(),
                        TextEntry::make('expedienteClinico.medicamentos_actuales')->label('Medicamentos actuales')->placeholder('Sin registro')->columnSpanFull(),
                        TextEntry::make('expedienteClinico.notas')->placeholder('Sin registro')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expedienteClinico.telefono')
                    ->label('Telefono')
                    ->placeholder('Sin registro'),
                Tables\Columns\TextColumn::make('citas_como_paciente_count')
                    ->label('Citas')
                    ->counts('citasComoPaciente'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() || auth()->user()?->isAsistente()),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', User::ROLE_PACIENTE);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->isMedico()
            || auth()->user()?->isAsistente();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isAsistente();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isAsistente();
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPacientes::route('/'),
            'create' => Pages\CreatePaciente::route('/create'),
            'view' => Pages\ViewPaciente::route('/{record}'),
            'edit' => Pages\EditPaciente::route('/{record}/edit'),
        ];
    }
}
