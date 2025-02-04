<?php

namespace App\Notifications;

use App\Enums\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomNotificationByEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $buttonText;
    public $url;

    public function __construct($title, $message, $buttonText = null, $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->buttonText = $buttonText;
        $this->url = $url;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaQueues()
    {
        return [
            'mail' => Queue::MAIL->value,
            'database' => Queue::MAIL->value,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->url){
            return (new MailMessage)
                ->subject($this->title)
                ->greeting('Hello '.$notifiable->first_name.',')
                ->line(new HtmlString($this->message))
                ->action($this->buttonText, $this->url);
        }else{
            return (new MailMessage)
                ->subject($this->title)
                ->greeting('Hello '.$notifiable->first_name.',')
                ->line(new HtmlString($this->message));
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array<string, string>
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => new HtmlString($this->message),
        ];
    }
}
