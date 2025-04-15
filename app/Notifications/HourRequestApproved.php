<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\HourRequest;

class HourRequestApproved extends Notification
{
    use Queueable;

    public $hourRequest;

    public function __construct(HourRequest $hourRequest)
    {
        $this->hourRequest = $hourRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Hour Request Approved')
                    ->line("Your hour request for {$this->hourRequest->requested_hours} hours on {$this->hourRequest->requested_date} has been approved.")
                    ->action('View Details', url('/hour-requests/' . $this->hourRequest->id))
                    ->line('Thank you for using Campus Jobs!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'hour_request_id' => $this->hourRequest->id,
            'status' => 'approved',
            'message' => "Your hour request for {$this->hourRequest->requested_hours} hours on {$this->hourRequest->requested_date} has been approved.",
        ];
    }
}
