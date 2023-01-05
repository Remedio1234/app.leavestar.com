<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class Invitation extends Mailable {

    use Queueable,
        SerializesModels;

    protected $user_register;
    protected $currentUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_register, $currentUser) {
        //
        $this->user_register = $user_register;
        $this->currentUser = $currentUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $from = $this->currentUser;
        $user_register = $this->user_register;
        $org = \App\Models\OrganisationStructure::where(['id' => $from->org_str_id])->first();
        $user = \App\User::where(['id' => $from->user_id])->first();
        $emailfrom = env('MAIL_From_person').' <'.env('MAIL_From').'>';
        $inTro[] = 'You are receiving this email because you have just been invited to LeaveStar by ' . '<b>' . $user->name . '</b> from <b>' . $org->name . '</b>';
        $outTro[] = 'If you have any other question, or unable to complete the registration procedure, please contact our support team support@leavestar.com';
        return $this->view('vendor.notifications.email')
                        ->subject('New User Invitation From LeaveStar')
                        ->with('introLines', $inTro)
                        ->with('actionText', 'Click to accept invitation')
                        ->with('actionUrl', 'http://app.leavestar.com/userRegisters/registerFromToken/' . $user_register->token)
                        ->with('level', 'primary')
                        ->with('outroLines', $outTro);
    }

}
