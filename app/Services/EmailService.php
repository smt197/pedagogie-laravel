<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\InscriptionApprenantMail;

class EmailService
{
    public function sendQrCodeEmail($email, $pdfFilePath, $mailData)
    {
        Mail::to($email)->send(new InscriptionApprenantMail($mailData, $pdfFilePath));
    }
}