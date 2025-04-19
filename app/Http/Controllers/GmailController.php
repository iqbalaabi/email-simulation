<?php

namespace App\Http\Controllers;

use App\Services\GmailService;
use Google\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GmailController extends Controller
{
    public function redirectToGmail()
    {
        $client = (new GmailService())->getClient();
        return redirect($client->createAuthUrl());
    }

    public function handleGmailCallback(Request $request)
    {
        $client = (new GmailService())->getClient();
        $accessToken = $client->fetchAccessTokenWithAuthCode($request->code);
        Storage::put('google/token.json', json_encode($accessToken));

        return redirect()->route('legalpdf.index');
    }
}

