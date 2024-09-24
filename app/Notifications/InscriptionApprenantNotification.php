<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InscriptionApprenantNotification extends Notification
{
    use Queueable;

    public $mailData;
    public $pdfFilePath;

    public function __construct($mailData, $pdfFilePath)
    {
        $this->mailData = $mailData;
        $this->pdfFilePath = $pdfFilePath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        Log::info('Sending email with data: ', $this->mailData);

        return (new MailMessage)
            ->subject('Inscription à la plateforme')
            ->view('pdfs.qr_code', ['mailData' => $this->mailData])
            ->attach(storage_path('app/public/' . $this->pdfFilePath), [
                'as' => 'qr_code.pdf',
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
