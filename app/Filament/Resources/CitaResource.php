<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CitaResource\Pages;
use App\Models\Cita;
use App\Models\User;
use App\Support\AppointmentSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CitaResource extends Resource
{
    protected static ?string $model = Cita::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Clinica';

    protected static ?string $navigationLabel = 'Citas';

    public static function form(Form $form): Form
    {
        $isDoctor = auth()->user()?->isMedico() ?? false;

        return $form
            ->schema([
                Forms\Components\Section::make('Cita')
                    ->schema([
                        Forms\Components\Select::make('paciente_id')
                            ->label('Paciente')
                            ->relationship(
                                name: 'paciente',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_PACIENTE),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled($isDoctor),
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
                            ->live()
                            ->disabled($isDoctor),
                        Forms\Components\DatePicker::make('fecha')
                            ->required()
                            ->native(false)
                            ->disabled($isDoctor),
                        Forms\Components\TimePicker::make('hora')
                            ->seconds(false)
                            ->minutesStep(60)
                            ->required()
                            ->disabled($isDoctor),
                        Forms\Components\Select::make('estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'confirmada' => 'Confirmada',
                                'cancelada' => 'Cancelada',
                            ])
                            ->required(),
                        Forms\Components\Placeholder::make('horario_referencia')
                            ->label('Horario del medico')
                            ->content(function (Get $get): string {
                                $doctor = User::find($get('medico_id'));

                                if (! $doctor) {
                                    return 'Selecciona un medico para ver sus bloques de atencion.';
                                }

                                $schedules = $doctor->horarios
                                    ->sortBy(['dia_semana', 'hora_inicio'])
                                    ->map(fn ($slot) => AppointmentSchedule::dayLabel($slot->dia_semana) . ' ' . substr($slot->hora_inicio, 0, 5) . ' - ' . substr($slot->hora_fin, 0, 5))
                                    ->implode(', ');

                                return $schedules ?: 'Este medico no tiene horarios configurados.';
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paciente.name')
                    ->label('Paciente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('medico.name')
                    ->label('Medico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora')
                    ->time('H:i'),
                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'confirmada',
                        'danger' => 'cancelada',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('medico_id')
                    ->label('Medico')
                    ->relationship('medico', 'name'),
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmada' => 'Confirmada',
                        'cancelada' => 'Cancelada',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['paciente', 'medico']);

        if (auth()->user()?->isMedico()) {
            $query->where('medico_id', auth()->id());
        }

        return $query;
    }

    public static function validateAppointment(array $data, ?Cita $record = null): array
    {
        $data = [
            'paciente_id' => $data['paciente_id'] ?? $record?->paciente_id,
            'medico_id' => $data['medico_id'] ?? $record?->medico_id,
            'fecha' => $data['fecha'] ?? $record?->fecha?->format('Y-m-d'),
            'hora' => $data['hora'] ?? ($record?->hora ? substr($record->hora, 0, 5) : null),
            'estado' => $data['estado'] ?? $record?->estado,
        ];

        $doctor = User::query()
            ->whereKey($data['medico_id'] ?? null)
            ->where('role', User::ROLE_MEDICO)
            ->first();

        if (! $doctor) {
            throw ValidationException::withMessages([
                'medico_id' => 'Debes seleccionar un medico valido.',
            ]);
        }

        if (! AppointmentSchedule::doctorWorksAt($doctor, $data['fecha'], $data['hora'])) {
            throw ValidationException::withMessages([
                'hora' => 'El medico no atiende en ese horario.',
            ]);
        }

        if (AppointmentSchedule::hasConflict($doctor, $data['fecha'], $data['hora'], $record?->id)) {
            throw ValidationException::withMessages([
                'hora' => 'Ya existe una cita para ese medico en ese bloque de tiempo.',
            ]);
        }

        $data['hora'] = AppointmentSchedule::normalizeTime($data['hora']);

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCitas::route('/'),
            'create' => Pages\CreateCita::route('/create'),
            'edit' => Pages\EditCita::route('/{record}/edit'),
        ];
    }
}
