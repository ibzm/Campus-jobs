<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\HourRequest;

class HourRequestDenied extends Notification
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
                    ->subject('Hour Request Denied')
                    ->line('Your requested hours have been denied due to insufficient remaining hours or exceeding the available limit.')
                    ->action('View Hour Request', url('/hour-requests/' . $this->hourRequest->id))
                    ->line('Please review the request and try again if necessary.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'hour_request_id' => $this->hourRequest->id,
            'status' => 'rejected',
            'message' => 'This hour request has been denied due to insufficient remaining hours or other constraints.',
        ];
    }
}
