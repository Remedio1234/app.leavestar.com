<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Comment;

class CommentsNotification extends Notification {

    use Queueable;

    private $leaveapplication;
    private $from;
    private $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Models\LeaveApplication $leaveapplication, $from, $type) {

        $this->leaveapplication = $leaveapplication;
        $this->from = $from;
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
        return (new MailMessage)
                        ->subject('You have a new comment From LeaveStar')
                        ->line(\App\User::find($this->from)->name . " has added a comment to their leave request")
                        ->action('View notification', 'https://app.leavestar.com/leaveApplication/manage#leaveappid'.$this->leaveapplication->id)
                        ->line('Thank you for using Leavestar!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
                //
        ];
    }

    public function toDatabase($notifiable) {
        return [
            'type' => $this->type,
            'from' => \App\User::find($this->from)->name,
            'leaveapplicationID' => $this->leaveapplication->id,
        ];
    }

}
