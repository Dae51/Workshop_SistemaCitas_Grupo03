<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administracion';

    protected static ?string $navigationLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options([
                                User::ROLE_ADMIN => 'Administrador',
                                User::ROLE_MEDICO => 'Medico',
                                User::ROLE_ASISTENTE => 'Asistente',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('especialidad_id')
                            ->label('Especialidad')
                            ->relationship('especialidad', 'nombre')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('role') === User::ROLE_MEDICO),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->minLength(8),
                        Forms\Components\Toggle::make('activo')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Rol')
                    ->formatStateUsing(fn (string $state): string => User::ROLES[$state] ?? $state)
                    ->colors([
                        'danger' => User::ROLE_ADMIN,
                        'success' => User::ROLE_MEDICO,
                        'warning' => User::ROLE_ASISTENTE,
                    ]),
                Tables\Columns\TextColumn::make('especialidad.nombre')
                    ->label('Especialidad')
                    ->placeholder('N/A'),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        User::ROLE_ADMIN => 'Administrador',
                        User::ROLE_MEDICO => 'Medico',
                        User::ROLE_ASISTENTE => 'Asistente',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_MEDICO,
                User::ROLE_ASISTENTE,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
