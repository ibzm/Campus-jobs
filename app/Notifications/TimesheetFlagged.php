<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Timesheet;

class TimesheetFlagged extends Notification
{
    use Queueable;

    public $timesheet;

    public function __construct(Timesheet $timesheet)
    {
        $this->timesheet = $timesheet;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Timesheet Flagged for Review')
                    ->line('A timesheet has been flagged for review due to exceeding weekly hours or low remaining hours.')
                    ->action('View Timesheet', url('/timesheets/' . $this->timesheet->id))
                    ->line('Please review and take appropriate action.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'timesheet_id' => $this->timesheet->id,
            'status' => 'flagged',
            'message' => 'This timesheet has been flagged for review.',
        ];
    }
}
