<?php

namespace App\Notifications;

use App\Models\VaccineSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class VaccineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VaccineSchedule $schedule) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', \NotificationChannels\Fcm\FcmChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vaccine    = $this->schedule->vaccine;
        $animal     = $this->schedule->userAnimal;
        $animalName = $animal->nickname ?? $animal->animal->name;
        $date       = $this->schedule->scheduled_date->format('M d, Y');

        return (new MailMessage)
            ->subject("💉 Vaccine Reminder: {$vaccine->name} for {$animalName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that your animal **{$animalName}** is due for the **{$vaccine->name}** vaccine.")
            ->line("📅 **Scheduled Date:** {$date}")
            ->action('View Vaccine Schedule', env('FRONTEND_URL', 'http://localhost:5173').'/vaccines')
            ->line("Please make sure to administer the vaccine on time to protect your animal's health.")
            ->salutation('Stay safe, The ADAN Team');
    }

    public function toDatabase(object $notifiable): array
    {
        $vaccine    = $this->schedule->vaccine;
        $animal     = $this->schedule->userAnimal;
        $animalName = $animal->nickname ?? $animal->animal->name;

        return [
            'type'          => 'vaccine_reminder',
            'title'         => "Vaccine Due: {$vaccine->name}",
            'body'          => "{$animalName} needs {$vaccine->name} on {$this->schedule->scheduled_date->format('M d, Y')}",
            'schedule_id'   => $this->schedule->id,
            'user_animal_id'=> $this->schedule->user_animal_id,
            'vaccine_id'    => $this->schedule->vaccine_id,
        ];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        $vaccine    = $this->schedule->vaccine;
        $animal     = $this->schedule->userAnimal;
        $animalName = $animal->nickname ?? $animal->animal->name;

        return FcmMessage::create()
            ->notification(
                FcmNotification::create()
                    ->title("💉 Vaccine Due: {$vaccine->name}")
                    ->body("{$animalName} needs {$vaccine->name} on {$this->schedule->scheduled_date->format('M d, Y')}")
            )
            ->data([
                'type'        => 'vaccine_reminder',
                'schedule_id' => (string) $this->schedule->id,
                'vaccine_id'  => (string) $this->schedule->vaccine_id,
            ]);
    }
}
