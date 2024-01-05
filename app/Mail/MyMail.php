<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $contentText;

    public function __construct($subjectText, $contentText)
    {
        $this->subjectText = $subjectText;
        $this->contentText = $contentText;
    }

    public function build()
    {
        return $this->view('emails.my_email')
                    ->subject($this->subjectText);
    }
}
