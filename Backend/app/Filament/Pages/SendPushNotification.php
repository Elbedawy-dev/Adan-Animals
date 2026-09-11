<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Services\PushNotificationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @property Form $form
 */
class SendPushNotification extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?int $navigationSort = 25;

    protected static string $view = 'filament.pages.send-push-notification';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->getRoleNames()->isEmpty() || $user->hasAnyRole(['admin', 'super_admin'])) {
            return true;
        }

        return $user->can('send_push_notifications');
    }

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_notifications');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.push.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament.push.title');
    }

    public function isPushConfigured(): bool
    {
        return app(PushNotificationService::class)->isConfigured();
    }

    public function mount(): void
    {
        $this->form->fill([
            'title' => '',
            'body' => '',
            'audience' => 'doctors',
            'user_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string|Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('filament.push.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('body')
                            ->label(__('filament.push.fields.body'))
                            ->required()
                            ->rows(5)
                            ->maxLength(2000),
                        Radio::make('audience')
                            ->label(__('filament.push.fields.audience'))
                            ->options([
                                'doctors' => __('filament.push.audience.doctors'),
                                'customers' => __('filament.push.audience.customers'),
                                'all' => __('filament.push.audience.all'),
                                'user' => __('filament.push.audience.user'),
                            ])
                            ->live()
                            ->required(),
                        Select::make('user_id')
                            ->label(__('filament.push.fields.user'))
                            ->visible(fn (Get $get): bool => $get('audience') === 'user')
                            ->required(fn (Get $get): bool => $get('audience') === 'user')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                $q = User::query()
                                    ->whereNotNull('fcm_token')
                                    ->orderBy('name')
                                    ->limit(50);

                                if ($search !== '') {
                                    $term = '%'.addcslashes($search, '%_\\').'%';
                                    $q->where(function ($inner) use ($term): void {
                                        $inner->where('name', 'like', $term)
                                            ->orWhere('email', 'like', $term);
                                    });
                                }

                                return $q->get()
                                    ->mapWithKeys(fn (User $u): array => [
                                        $u->id => $u->name.' ('.$u->email.')',
                                    ])
                                    ->all();
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                if ($value === null || $value === '') {
                                    return null;
                                }

                                $u = User::query()->find($value);

                                return $u instanceof User ? $u->name.' ('.$u->email.')' : null;
                            })
                            ->dehydrated(fn (Get $get): bool => $get('audience') === 'user'),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    public function send(): void
    {
        $push = app(PushNotificationService::class);

        if (! $push->isConfigured()) {
            Notification::make()
                ->title(__('filament.push.notify_not_configured_title'))
                ->body(__('filament.push.notify_not_configured_body'))
                ->danger()
                ->send();

            return;
        }

        try {
            $state = $this->form->getState();
            $audience = (string) $state['audience'];
            $userId = isset($state['user_id']) ? (int) $state['user_id'] : null;

            $count = $push->broadcast(
                (string) $state['title'],
                (string) $state['body'],
                $audience,
                $audience === 'user' ? $userId : null,
            );
        } catch (Throwable $e) {
            Log::error('Admin push broadcast failed', ['exception' => $e]);
            Notification::make()
                ->title(__('filament.push.notify_error_title'))
                ->danger()
                ->send();

            return;
        }

        if ($count === 0) {
            Notification::make()
                ->title(__('filament.push.notify_no_recipients_title'))
                ->body(__('filament.push.notify_no_recipients_body'))
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('filament.push.notify_sent_title'))
            ->body(__('filament.push.notify_sent_body', ['count' => $count]))
            ->success()
            ->send();

        $this->form->fill([
            'title' => '',
            'body' => '',
            'audience' => $state['audience'] ?? 'doctors',
            'user_id' => null,
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label(__('filament.push.action_send'))
                ->submit('send')
                ->disabled(fn (): bool => ! $this->isPushConfigured()),
        ];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Start;
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
