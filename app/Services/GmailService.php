<?php
namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Illuminate\Support\Facades\Storage;

class GmailService {
    public function gmailAuth()
    {
        $client = new Client();
        $client->setAuthConfig([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uris' => [env('GOOGLE_REDIRECT_URI')],
        ]);
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope('https://www.googleapis.com/auth/gmail.modify');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        return redirect($client->createAuthUrl());
    }

    public function oauth2callback(Request $request)
    {
        $client = new Client();
        $client->setAuthConfig([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uris' => [env('GOOGLE_REDIRECT_URI')],
        ]);
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope('https://www.googleapis.com/auth/gmail.modify');

        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        Storage::put('gmail-token.json', json_encode($token));

        return 'Token saved!';
    }

    public function getClient()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json')); // Your downloaded client secret
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(route('gmail.callback'));
        $client->addScope(Gmail::GMAIL_MODIFY);

        // Load token
        if (Storage::exists('google/token.json')) {
            $client->setAccessToken(json_decode(Storage::get('google/token.json'), true));
        }

        // Refresh if expired
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                Storage::put('google/token.json', json_encode($client->getAccessToken()));
            }
        }

        return $client;
    }

    public function getGmailService(): Gmail
    {
        return new Gmail($this->getClient());
    }


}