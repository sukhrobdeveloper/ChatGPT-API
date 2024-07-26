<?php
session_start();

use GuzzleHttp\Client;

require 'vendor/autoload.php';

header('Content-Type: application/json');

$apiKey = 'API_KEY';
$model = 'gpt-3.5-turbo';
$endpoint = 'https://api.openai.com/v1/chat/completions';

$userMessage = trim($_POST['message']); 

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

if (!empty($userMessage)) {
    $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $userMessage];
}

$_SESSION['chat_history'] = array_filter($_SESSION['chat_history'], function($message) {
    return !empty($message['content']);
});

$client = new Client();

try {
    $response = $client->post($endpoint, [
        'headers' => [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => $model,
            'messages' => $_SESSION['chat_history'],
            'max_tokens' => 50,
        ],
    ]);

    $body = $response->getBody();
    $data = json_decode($body, true);

    if (isset($data['choices'][0]['message']['content'])) {
        $botMessage = $data['choices'][0]['message']['content'];
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $botMessage];
        echo json_encode(['message' => $botMessage]);
    } else {
        echo json_encode(['error' => 'No response from ChatGPT']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);

    error_log("Exception caught: " . $e->getMessage());
}
?>
