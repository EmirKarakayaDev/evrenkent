<?php

namespace App\Console\Commands;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Notifications\ContentPublished;
use Illuminate\Console\Command;

class PublishScheduledBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Planlanan yayın tarihi gelmiş "Onaylandı" kitapları otomatik "Yayında"ya çevirir.';

    public function handle(): int
    {
        $books = Book::where('status', ContentStatus::Onaylandi)
            ->whereNotNull('scheduled_publish_at')
            ->where('scheduled_publish_at', '<=', now())
            ->with('author')
            ->get();

        foreach ($books as $book) {
            $book->update([
                'status' => ContentStatus::Yayinda,
                'published_at' => $book->scheduled_publish_at,
            ]);

            $book->author->notify(new ContentPublished($book));
        }

        $this->info("{$books->count()} kitap yayınlandı.");

        return self::SUCCESS;
    }
}
