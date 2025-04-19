<?php
namespace App\Services;

use Google\Service\Gmail\Message;
use Google\Service\Gmail;

class GmailMessageService
{

    public function sendEmail(Gmail $gmail, string $from, string $to, string $subject, string $body, string $threadId = null, array $extraHeaders = []): array
    {
        $headers = [
            "From: <$from>",
            "To: <$to>",
            "Subject: $subject",
            "Content-Type: text/plain; charset=utf-8",
        ];
    
        foreach ($extraHeaders as $key => $value) {
            $headers[] = "$key: $value"; // ✅ RFC 2822 style (no angle brackets for Message-ID values)
        }
    
        $rawMessage = implode("\r\n", $headers) . "\r\n\r\n" . $body;
        $encodedMessage = base64_encode($rawMessage);
        $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
    
        $message = new \Google\Service\Gmail\Message();
        $message->setRaw($encodedMessage);
    
        if ($threadId) {
            $message->setThreadId($threadId);
        }
    
        // Send the message
        $sent = $gmail->users_messages->send('me', $message);
    
        // Fetch full message to get its Message-ID
        $fullMessage = $gmail->users_messages->get('me', $sent->getId(), ['format' => 'metadata']);
    
        $msgIdHeader = collect($fullMessage->getPayload()->getHeaders())
                        ->firstWhere('name', 'Message-Id');
    
        return [
            'messageId' => $msgIdHeader?->getValue(),
            'threadId' => $sent->getThreadId(),
        ];
    }
    

}
