<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

function sendMessage(string $dest, string $content)
{
    $sid = getEnvVar()["TWILIO_SID"];
    $token = getEnvVar()["TWILIO_TOKEN"];
    $twilioNumber = getEnvVar()["TWILIO_NUMBER"];

    $client = new Client($sid, $token);

    try {
        $message = $client->messages->create(
            $dest,
            [
                'from' => $twilioNumber,
                'body' => $content
            ]
        );

        echo "SMS envoyé avec succès ! SID : " . $message->sid;

    } catch (\Exception $e) {
        echo "Erreur lors de l'envoi du SMS : " . $e->getMessage();
    }
}