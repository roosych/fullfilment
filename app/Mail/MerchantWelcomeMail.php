<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MerchantWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Merchant $merchant,
        public string $password
    ) {
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Добро пожаловать! Ваши данные для входа')
            ->view('emails.merchant.welcome')
            ->with([
                'merchant' => $this->merchant,
                'password' => $this->password,
                'user' => $this->merchant->user,
            ]);
    }
}

