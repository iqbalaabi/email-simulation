<!DOCTYPE html>
<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            .email { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        </style>
    </head>
    <body>
        <h2>Email Correspondence</h2>
        @foreach($emails as $email)
            <div class="email">
                <p><strong>From:</strong> {{ $email['from'] }}</p>
                <p><strong>To:</strong> {{ $email['to'] }}</p>
                <p><strong>Subject:</strong> {{ $email['subject'] }}</p>
                <p><strong>Time:</strong> {{ $email['timestamp'] }}</p>
                <p>{!! nl2br(e($email['body'])) !!}</p>
            </div>
        @endforeach
    </body>
</html>