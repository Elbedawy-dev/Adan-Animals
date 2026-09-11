<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Support\PermissionRegistry;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 50;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_users');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.role.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.role.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.role.plural');
    }

    /** Only super admins and users without any role can manage roles. */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->getRoleNames()->isEmpty() || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public static function form(Form $form): Form
    {
        $sections = [];

        foreach (PermissionRegistry::grouped() as $group => $permissions) {
            $options = collect($permissions)
                ->mapWithKeys(fn (string $perm) => [
                    $perm => __("filament.permissions.{$perm}"),
                ])
                ->all();

            $sections[] = Section::make(__("filament.permission_groups.{$group}"))
                ->schema([
                    CheckboxList::make("perm_{$group}")
                        ->hiddenLabel()
                        ->options($options)
                        ->columns(2)
                        ->bulkToggleable(),
                ])
                ->collapsible()
                ->collapsed(false);
        }

        return $form->schema([
            TextInput::make('name')
                ->label(__('filament.labels.role_name'))
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true)
                ->columnSpanFull(),

            ...$sections,
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.labels.role_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('filament.labels.permissions_count'))
                    ->badge()
                    ->color('primary'),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(__('filament.labels.users_count'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->label(__('filament.labels.created_at')),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
