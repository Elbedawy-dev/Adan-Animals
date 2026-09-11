<?php

namespace App\Notifications;

use App\Models\DiseaseReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ReportStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DiseaseReport $report) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', \NotificationChannels\Fcm\FcmChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->greeting("Hello {$notifiable->name},");

        if ($this->report->isApproved()) {
            $mail->subject('✅ Your Report Has Been Approved')
                ->line("Great news! Your report **\"{$this->report->title}\"** has been reviewed and **approved** by our veterinary team.")
                ->line('Disease alerts have been sent to all breeders in '.($this->report->region?->name ?? 'the area').'.')
                ->action('View Report', env('FRONTEND_URL', 'http://localhost:5173').'/reports/'.$this->report->id);
        } else {
            $mail->subject('❌ Your Report Could Not Be Confirmed')
                ->line("Your report **\"{$this->report->title}\"** has been reviewed but could not be confirmed at this time.")
                ->line("**Reason:** {$this->report->rejection_reason}")
                ->line('If you believe this is an error or wish to resubmit with additional information, please contact us.')
                ->action('View Your Reports', env('FRONTEND_URL', 'http://localhost:5173').'/reports');
        }

        return $mail->salutation('Regards, The ADAN Veterinary Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'      => 'report_status',
            'title'     => $this->report->isApproved() ? '✅ Report Approved' : '❌ Report Rejected',
            'body'      => $this->report->isApproved()
                ? "Your report \"{$this->report->title}\" was approved. Alerts sent to ".($this->report->region?->name ?? 'the region').'.'
                : "Your report \"{$this->report->title}\" was rejected. Reason: {$this->report->rejection_reason}",
            'report_id' => $this->report->id,
            'status'    => $this->report->status,
        ];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        if ($this->report->isApproved()) {
            $title = __('api.fcm_report_approved_title');
            $body  = __('api.fcm_report_approved_body', [
                'title'  => $this->report->title,
                'region' => $this->report->region?->name ?? '',
            ]);
        } else {
            $title = __('api.fcm_report_rejected_title');
            $body  = __('api.fcm_report_rejected_body', [
                'title'  => $this->report->title,
                'reason' => $this->report->rejection_reason ?? '',
            ]);
        }

        return FcmMessage::create()
            ->notification(FcmNotification::create()->title($title)->body($body))
            ->data([
                'type'      => 'report_status',
                'report_id' => (string) $this->report->id,
                'status'    => $this->report->status ?? '',
            ]);
    }
}
