<?php

namespace App\Notifications;

use App\Notifications\Concerns\DescribesContent;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class ContentPublished extends Notification
{
    use DescribesContent, Queueable;

    public function __construct(protected Model $content) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->contentLabel($this->content).' yayınlandı',
            'body' => "\"{$this->content->title}\" artık yayında, okurlar görebiliyor.",
            'url' => $this->contentUrl($this->content),
        ];
    }
}
