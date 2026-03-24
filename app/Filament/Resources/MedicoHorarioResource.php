<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicoHorarioResource\Pages;
use App\Models\MedicoHorario;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MedicoHorarioResource extends Resource
{
    protected static ?string $model = MedicoHorario::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Clinica';

    protected static ?string $navigationLabel = 'Horarios medicos';

    protected static ?string $slug = 'horarios-medicos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('medico_id')
                    ->label('Medico')
                    ->relationship(
                        name: 'medico',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_MEDICO),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (?MedicoHorario $record): bool => auth()->user()?->isMedico() && $record !== null),
                Forms\Components\Select::make('dia_semana')
                    ->label('Dia')
                    ->options(MedicoHorario::DAYS)
                    ->required(),
                Forms\Components\TimePicker::make('hora_inicio')
                    ->label('Hora inicio')
                    ->seconds(false)
                    ->required(),
                Forms\Components\TimePicker::make('hora_fin')
                    ->label('Hora fin')
                    ->seconds(false)
                    ->required()
                    ->after('hora_inicio'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medico.name')
                    ->label('Medico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dia_semana')
                    ->label('Dia')
                    ->formatStateUsing(fn (int $state): string => MedicoHorario::DAYS[$state] ?? (string) $state),
                Tables\Columns\TextColumn::make('hora_inicio')->time('H:i'),
                Tables\Columns\TextColumn::make('hora_fin')->time('H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->isMedico()) {
            $query->where('medico_id', auth()->id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicoHorarios::route('/'),
            'create' => Pages\CreateMedicoHorario::route('/create'),
            'edit' => Pages\EditMedicoHorario::route('/{record}/edit'),
        ];
    }
}
