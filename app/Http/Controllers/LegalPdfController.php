<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GmailService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class LegalPdfController extends Controller
{
    public function index()
    {
        return view('legalpdf.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string',
            'from' => 'nullable|email',
            'to' => 'nullable|email',
        ]);

        $gmail = (new GmailService())->getGmailService();

        $query = [];

        if ($request->subject) $query[] = 'subject:' . $request->subject;
        if ($request->from)    $query[] = 'from:' . $request->from;
        if ($request->to)      $query[] = 'to:' . $request->to;

        $queryString = implode(' ', $query);

        $results = $gmail->users_messages->listUsersMessages('me', ['q' => $queryString]);
        $messages = $results->getMessages();

        if (!$messages || count($messages) === 0) {
            return back()->with('error', 'No matching threads found.');
        }

        $msg = $gmail->users_messages->get('me', $messages[0]->getId());
        $threadId = $msg->getThreadId();

        return view('legalpdf.result', compact('threadId'));
    }

    public function generate($threadId)
    {
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', 0);
        $gmail = (new GmailService())->getGmailService();
        $thread = $gmail->users_threads->get('me', $threadId);

        $emails = [];

        foreach ($thread->getMessages() as $msg) {
            $headers = collect($msg->getPayload()->getHeaders());
            $from = $headers->firstWhere('name', 'From')?->value ?? '';
            $to = $headers->firstWhere('name', 'To')?->value ?? '';
            $date = $headers->firstWhere('name', 'Date')?->value ?? '';
            $subject = $headers->firstWhere('name', 'Subject')?->value ?? '';
            $body = $this->extractBody($msg->getPayload());

            $emails[] = compact('from', 'to', 'date', 'subject', 'body');
        }

        $html = View::make('pdf.legal_thread', compact('emails'))->render();
        $filename = 'legal_thread_' . now()->format('Ymd_His') . '.pdf';

        Pdf::loadHTML($html)->save(storage_path("app/public/$filename"));

        return response()->download(storage_path("app/public/$filename"));
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
