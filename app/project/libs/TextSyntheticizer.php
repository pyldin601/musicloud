<?php

namespace app\project\libs;

use GuzzleHttp\Client;

class TextSyntheticizer
{
    public static function say($command)
    {
        $apiKey = $_ENV['GOOGLE_API_KEY'];

        $requestBody = [
            'input' => ['text' => $command],
            'voice' => [
                'languageCode' => 'en-GB',
                'name' => 'en-GB-Neural2-B'
            ],
            'audioConfig' => [
                'audioEncoding' => 'MP3'
            ]
        ];

        $client = new Client([
            'timeout' => 10.0,
        ]);

        error_log("Requesting Google TTS API with command: " . $apiKey);

        $response = $client->request('POST', "https://texttospeech.googleapis.com/v1/text:synthesize", [
            'query' => ['key' => $apiKey],
            'json' => $requestBody,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['audioContent'])) {
            throw new \Exception("No audioContent in response.");
        }

        header('Content-Type: audio/mpeg');
        header('Content-Disposition: inline; filename="speech.mp3"');
        echo base64_decode($data['audioContent']);

    }
}
