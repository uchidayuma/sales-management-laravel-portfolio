<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArticleNoticeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $article;

    /**
     * Create a new message instance.
     */
    public function __construct($article)
    {
        $this->article = $article;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【サンプルFC】本部より新しいお知らせがございます')
                    ->markdown('emails.admin.articles.notice');
    }
}
