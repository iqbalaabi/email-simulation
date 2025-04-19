<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .email { page-break-after: always; margin-bottom: 30px; }
        .meta { background: #f0f0f0; padding: 10px; margin-bottom: 5px; }
        .body { white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Legal Email Thread</h1>

    @foreach($emails as $email)
        <div class="email">
            <div class="meta">
                <strong>From:</strong> {{ $email['from'] }}<br>
                <strong>To:</strong> {{ $email['to'] }}<br>
                <strong>Date:</strong> {{ $email['date'] }}<br>
                <strong>Subject:</strong> {{ $email['subject'] }}
            </div>
            <div class="body">
                {!! nl2br(e($email['body'])) !!}
            </div>
        </div>
    @endforeach
</body>
</html>
