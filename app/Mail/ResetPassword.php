<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $time;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link, $time)
    {
        $this->link = $link;
        $this->time = $time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.resetpassword')
            ->subject('Reset Password')
            ->from('noreply@appsoho.com', 'appsoho.com')
            ->with([
                'link' => $this->link,
                'time' => $this->time
            ]);
    }
}
