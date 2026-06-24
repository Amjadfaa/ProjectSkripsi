<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NotifikasiKadaluarsaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $kartuList,
        public string $namaInstansi
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Notifikasi Masa Berlaku Kartu PAS - ' . $this->namaInstansi,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notifikasi-kadaluarsa',
        );
    }
}