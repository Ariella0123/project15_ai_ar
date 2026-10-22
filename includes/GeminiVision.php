<?php
declare(strict_types=1);

require_once __DIR__ . '/AIProvider.php';

class GeminiVision extends AIProvider {
    public function recognize(string $imageBase64): ?array {
        if (empty($this->apiKey)) return null;

// 确保变量中没有多余的 "models/" 前缀
    $modelName = preg_replace('/^models\//', '', $this->model);

    // 拼接最终的 API 地址
    $url = "https://generativelanguage.googleapis.com/v1beta/interactions";        
        $payload = [
            'model' => $this->model,
            'input' => [
                [
                    ['type' => 'text', 'text' => $this->getPrompt()],
                    ['type' => 'image', 'data' => $imageBase64, 'mime_type' => 'image/jpeg']
                    
                ]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => $this->timeout
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) return null;

        $result = json_decode($response, true);
        
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        
        return $text ? json_decode($text, true) : null;
    }
}