<?php

use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;

require __DIR__ . "/vendor/autoload.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $message = $_POST["message"];
    $phoneNumbers = explode("\n", $_POST["phoneNumbers"]);

// Directly setting API URL and API Key
$apiURL = "https://kq2pdn.api.infobip.com"; 
$apiKey = "bedbfe27c28b1db036841843bd9b8f14-30152fd0-eb7c-4b89-b8b2-f60d143c391c"; 

    if (!$apiURL || !$apiKey) {
        die('API URL or API Key is not set in environment variables.');
    }

    $configuration = new Configuration(host: $apiURL, apiKey: $apiKey);
    $api = new SmsApi(config: $configuration);

    $destinations = [];
    foreach ($phoneNumbers as $phoneNumber) {
        $phoneNumber = trim($phoneNumber);
        if (!empty($phoneNumber)) {
            $destinations[] = new SmsDestination(to: $phoneNumber);
        }
    }

    if (empty($destinations)) {
        echo "No valid phone numbers provided.";
        return;
    }

    $theMessage = new SmsTextualMessage(
        destinations: $destinations,
        text: $message,
        from: "ST.MoniqueVHOA"
    );

    $request = new SmsAdvancedTextualRequest(messages: [$theMessage]);

    try {
        $response = $api->sendSmsMessage($request);
        echo "Bulk SMS Message Sent";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
