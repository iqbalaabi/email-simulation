<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GmailService;

class FindGmailThread extends Command
{
    protected $signature = 'legalpdf:find-thread 
        {--subject= : Subject to match (optional)} 
        {--from= : Filter by sender (optional)} 
        {--to= : Filter by recipient (optional)}';

    protected $description = 'Search Gmail for a thread matching subject/from/to and return the threadId';

    public function handle()
    {
        $gmail = (new GmailService())->getGmailService();

        $query = [];

        if ($this->option('subject')) {
            $query[] = 'subject:' . $this->option('subject');
        }

        if ($this->option('from')) {
            $query[] = 'from:' . $this->option('from');
        }

        if ($this->option('to')) {
            $query[] = 'to:' . $this->option('to');
        }

        $queryString = implode(' ', $query);
        $this->info("Searching Gmail for: \"$queryString\"");

        $results = $gmail->users_messages->listUsersMessages('me', ['q' => $queryString]);

        if (count($results->getMessages()) === 0) {
            $this->error('No matching threads found.');
            return;
        }

        $messages = $results->getMessages();

        // Pick the most recent message
        $messageId = $messages[0]->getId();
        $message = $gmail->users_messages->get('me', $messageId);
        $threadId = $message->getThreadId();

        $this->info("Thread ID found: $threadId");
        $this->info("Use this to generate the PDF:");
        $this->line("php artisan legalpdf:generate-pdf $threadId");
    }
}

