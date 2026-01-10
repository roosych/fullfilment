<?php

namespace App\Jobs;

use App\Mail\MerchantWelcomeMail;
use App\Models\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMerchantWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Merchant $merchant,
        public string $password
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Проверяем, что у мерчанта есть email
        if ($this->merchant->user->email) {
            Mail::to($this->merchant->user->email)
                ->send(new MerchantWelcomeMail($this->merchant, $this->password));
        }
    }
}

