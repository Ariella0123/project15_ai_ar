<?php
declare(strict_types=1);

require_once __DIR__ . '/AIProvider.php';

class AgnesVision extends AIProvider {
    public function recognize(string $imageBase64): ?array {
        if (empty($this->apiKey)) return null;

        $url = "https://api.agnes.ai/v1/vision/analyze";
        $payload = [
            'model' => $this->model,
            'image' => $imageBase64,
            'prompt' => $this->getPrompt()
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => $this->timeout
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) return null;

        return json_decode($response, true)['result'] ?? null;
    }
}