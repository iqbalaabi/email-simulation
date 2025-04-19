<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GmailService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateLegalThreadPdf extends Command
{
    protected $signature = 'legalpdf:generate-pdf {threadId}';
    protected $description = 'Fetch Gmail thread and generate a large PDF from all emails';

    public function handle()
    {
        $threadId = $this->argument('threadId');
        $gmail = (new GmailService())->getGmailService();

        $this->info("Fetching thread: $threadId");

        $thread = $gmail->users_threads->get('me', $threadId);
        $messages = $thread->getMessages();

        $emails = [];

        foreach ($messages as $msg) {
            $headers = collect($msg->getPayload()->getHeaders());
            $from = $headers->firstWhere('name', 'From')?->value ?? '';
            $to = $headers->firstWhere('name', 'To')?->value ?? '';
            $date = $headers->firstWhere('name', 'Date')?->value ?? '';
            $subject = $headers->firstWhere('name', 'Subject')?->value ?? '';
            $body = $this->extractBody($msg->getPayload());

            $emails[] = compact('from', 'to', 'date', 'subject', 'body');
        }

        $this->info("Rendering Blade view...");

        $html = view('pdf.legal_thread', compact('emails'))->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4');

        $filename = 'legal_thread_' . now()->format('Ymd_His') . '.pdf';
        $pdf->save(storage_path("app/public/$filename"));

        $this->info("PDF saved: storage/app/public/$filename");
    }

    private function extractBody($payload): string
    {
        $parts = $payload->getParts();
        if ($parts) {
            foreach ($parts as $part) {
                if ($part->getMimeType() === 'text/plain') {
                    return base64_decode(strtr($part->getBody()->getData(), '-_', '+/'));
                }
            }
        }

        return base64_decode(strtr($payload->getBody()->getData(), '-_', '+/')) ?? '';
    }
}

