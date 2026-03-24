<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpedienteClinicoResource\Pages;
use App\Models\ExpedienteClinico;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpedienteClinicoResource extends Resource
{
    protected static ?string $model = ExpedienteClinico::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Clinica';

    protected static ?string $navigationLabel = 'Expedientes';

    protected static ?string $slug = 'expedientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Expediente')
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
                            ->required(),
                        Forms\Components\TextInput::make('telefono')->tel()->maxLength(30),
                        Forms\Components\TextInput::make('direccion')->maxLength(255),
                        Forms\Components\DatePicker::make('fecha_nacimiento'),
                        Forms\Components\TextInput::make('tipo_sangre')->maxLength(5),
                        Forms\Components\Textarea::make('alergias')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('antecedentes')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('medicamentos_actuales')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('notas')->rows(4)->columnSpanFull(),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telefono')->placeholder('Sin registro'),
                Tables\Columns\TextColumn::make('tipo_sangre')
                    ->label('Sangre')
                    ->placeholder('Sin registro'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() || auth()->user()?->isMedico()),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpedientesClinicos::route('/'),
            'create' => Pages\CreateExpedienteClinico::route('/create'),
            'view' => Pages\ViewExpedienteClinico::route('/{record}'),
            'edit' => Pages\EditExpedienteClinico::route('/{record}/edit'),
        ];
    }
}
