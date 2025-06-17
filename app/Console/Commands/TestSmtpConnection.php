<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class TestSmtpConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {--to= : Mail address to test (default: MAIL_FROM_ADDRESS)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify if the current SMTP setup can send a mail successfully';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $to = $this->option('to') ?? config('mail.from.address');

        if (!$to) 
        {
            $this->error('Was not specified a valid email address.');
            return Command::FAILURE;
        }

        try {
            Mail::raw('This is a test mail to checkout the SMTP setup.', function ($message) use ($to) {
                $message->to($to)
                        ->subject('Test mail SMTP - Laravel');
            });

            $this->info("Mail sent successfully to {$to}.");
            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error("Error al enviar el correo: " . $e->getMessage());
            Log::error('Failed test SMTP', ['exception' => $e]);
            return Command::FAILURE;
        }
    }
}
