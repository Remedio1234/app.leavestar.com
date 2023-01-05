<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationNotification extends Notification {

    use Queueable;

    public $user_register;
    public $currentUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user_register, $currentUser) {
        $this->user_register = $user_register;
        $this->currentUser = $currentUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        $from = $this->currentUser;
        $user_register = $this->user_register;
        $org = \App\Models\OrganisationStructure::where(['id' => $from->org_str_id])->first();
        $user = \App\User::where(['id' => $from->user_id])->first();
        return (new MailMessage)
                        ->subject('New User Invitation From LeaveStar')
                        ->line('You are receiving this email because you have just been invited to LeaveStar by ' . '<b>' . $user->name . '</b> from <b>' . $org->name . '</b>')
                        ->action('Reset Password', url('http://app.leavestar.com/userRegisters/registerFromToken/' . $user_register->token))
                        ->line('If you have any other question, or unable to complete the registration procedure, please contact our support team support@leavestar.com');
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

}
