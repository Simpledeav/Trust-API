<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseBackup extends Mailable
{
    use Queueable, SerializesModels;

    public string $filePath;
    public string $filename;
    public string $fileSize;
    public string $backupDate;

    /**
     * Create a new message instance.
     */
    public function __construct(string $filePath, string $filename, string $fileSize, string $backupDate)
    {
        $this->filePath = $filePath;
        $this->filename = $filename;
        $this->fileSize = $fileSize;
        $this->backupDate = $backupDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Database Backup',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.backup',
            with: [
                'fileName' => $this->filename,
                'fileSize' => $this->fileSize,
                'backupDate' => $this->backupDate,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
                ->as($this->filename)
                ->withMime('application/sql'),
        ];
    }
}
