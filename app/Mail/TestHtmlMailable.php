<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestHtmlMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public ?string $template = null,
        public array $data = [],
        public ?string $htmlBody = null,
        public array $attachmentsList = [],
    ) {
    }

    public function build(): static
    {
        $mail = $this->subject($this->subjectLine);

        if ($this->template) {
            $mail->view($this->template, $this->data);
        } else {
            $mail->html($this->htmlBody ?? '');
        }

        foreach ($this->attachmentsList as $att) {
            $mail->attach($att['path'], array_filter([
                'as' => $att['name'] ?? null,
                'mime' => $att['mime'] ?? null,
            ]));
        }

        return $mail;
    }
}

