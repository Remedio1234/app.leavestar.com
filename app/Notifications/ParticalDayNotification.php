<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ParticalDayNotification extends Notification {

    use Queueable;

    private $leaveapplication;
    private $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Models\LeaveApplication $leaveapplication, $type = "particalLeaves") {

        $this->leaveapplication = $leaveapplication;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {

        $subject = "You need to create the leave in Xero manually.";

        $url = action('LeaveApplicationController@manageLeave') . '#leaveappid' . $this->leaveapplication->id;

        return (new MailMessage)
                        ->subject($subject)
                        ->line('A partical-day leave has been approved. You need to create this leave in Xero manually' . ".")
                        ->action('See Leave Application', $url)
                        ->line('Thank you for using LeaveStar!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        
    }

    public function toDatabase($notifiable) {
        return [
            'leaveapplicationID' => $this->leaveapplication->id,
            'type' => $this->type,
        ];
    }

}
