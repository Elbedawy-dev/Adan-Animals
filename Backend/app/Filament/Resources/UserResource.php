<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\Region;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'users';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_users');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.user.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.user.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.user.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(100)
                ->label(__('filament.labels.name')),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->label(__('filament.labels.email')),

            TextInput::make('phone')
                ->nullable()
                ->tel()
                ->label(__('filament.labels.phone')),

            Select::make('role')
                ->label(__('filament.labels.role'))
                ->options([
                    'customer' => __('filament.user_roles.customer_breeder'),
                    'doctor'   => __('filament.user_roles.doctor_vet'),
                ])
                ->required()
                ->default('customer')
                ->live(),

            // ── Dashboard roles (Spatie) — only relevant for doctor/staff accounts ──
            Select::make('spatie_roles')
                ->label(__('filament.labels.dashboard_role'))
                ->helperText(__('filament.labels.dashboard_role_help'))
                ->multiple()
                ->options(
                    \Spatie\Permission\Models\Role::where('guard_name', 'web')
                        ->pluck('name', 'name')
                )
                ->preload()
                ->searchable()
                ->visible(fn (Get $get): bool => $get('role') === 'doctor'),

            TextInput::make('password')
                ->password()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn ($state) => filled($state))
                ->label(__('filament.labels.password')),

            Select::make('region_id')
                ->label(__('filament.labels.region'))
                ->options(
                    Region::with('city.governorate')
                        ->get()
                        ->mapWithKeys(fn ($r) => [
                            $r->id => "{$r->name} — {$r->city->name}, {$r->city->governorate->name}",
                        ])
                )
                ->searchable()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60)->label(__('filament.labels.id')),
                TextColumn::make('name')->label(__('filament.labels.name'))->searchable()->sortable(),
                TextColumn::make('email')->label(__('filament.labels.email'))->searchable(),
                TextColumn::make('phone')->placeholder('—')->toggleable()->label(__('filament.labels.phone')),
                TextColumn::make('region.name')->label(__('filament.labels.region'))->placeholder('—')->sortable(),
                TextColumn::make('role')
                    ->label(__('filament.labels.role'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'customer' => __('filament.user_roles.customer'),
                        'doctor'   => __('filament.user_roles.doctor'),
                        default    => (string) $state,
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'doctor'   => 'success',
                        'customer' => 'info',
                        default    => 'gray',
                    }),
                TextColumn::make('roles.name')
                    ->label(__('filament.labels.dashboard_role'))
                    ->badge()
                    ->color('warning')
                    ->separator(',')
                    ->placeholder('—'),
                IconColumn::make('email_verified_at')
                    ->label(__('filament.labels.verified'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->email_verified_at !== null),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable()->label(__('filament.labels.created_at')),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('filament.labels.role'))
                    ->options([
                        'customer' => __('filament.user_roles.customer'),
                        'doctor'   => __('filament.user_roles.doctor'),
                    ]),
                SelectFilter::make('region_id')
                    ->label(__('filament.labels.region'))
                    ->options(Region::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id', '!=', auth()->id());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
