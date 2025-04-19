<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GmailService;
use App\Services\GmailMessageService;
use PhpOffice\PhpWord\IOFactory;

class SendLegalEmails extends Command
{
    protected $signature = 'legalpdf:send-thread';
    protected $description = 'Send 25 back-and-forth real emails in a single Gmail thread';
    const INPUT_DOCX_FILE_PATH = 'app/file-1mb.docx';
    const SENDER_A='';
    const SENDER_B = '';

    public function handle()
    {
        $gmailService = new GmailService();
        $gmail = $gmailService->getGmailService();

        // Load .docx and extract full content
        $docxPath = storage_path(self::INPUT_DOCX_FILE_PATH);
        $text = $this->extractFullDocxText($docxPath);

        // First email: from A to B
        $from = self::SENDER_A;
        $to = self::SENDER_B;
        $subject = 'Legal Discussion: Threaded Chain';

        $this->info('Sending first email...');

        $messageService = new GmailMessageService();
        $response = $messageService->sendEmail($gmail, $from, $to, $subject, $text);

        $threadId = $response['threadId'];
        $firstMessageId = $response['messageId'];

        $this->info("First message sent. Thread ID: $threadId");

        // Send 24 more replies alternating A and B
        $referenceIds = [$firstMessageId];
        $currentMessageId = $firstMessageId;

        for ($i = 2; $i <= 25; $i++) {
            [$from, $to] = [$to, $from];

            $this->info("Sending reply #$i from $from to $to");

            $headers = [
                'In-Reply-To' => $currentMessageId,
                'References' => implode(' ', $referenceIds),
            ];

            $reply = $messageService->sendEmail($gmail, $from, $to, $subject, $text, $threadId, $headers);

            $currentMessageId = $reply['messageId'];
            $referenceIds[] = $currentMessageId;

            sleep(1);
        }


        $this->info('✅ Threaded conversation complete.');
    }

    private function extractFullDocxText($filePath): string
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        return $text;
    }
}

