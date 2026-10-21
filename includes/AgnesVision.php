<?php
declare(strict_types=1);

require_once __DIR__ . '/AIProvider.php';

class AgnesVision extends AIProvider {
    public function recognize(string $imageBase64): ?array {
        if (empty($this->apiKey)) return null;

        $url = "https://api.agnes.ai/v1/vision/analyze"; // 如果使用了 Chat Completions 接口，通常 endpoint 为 https://api.agnes.ai/v1/chat/completions

        // 构造符合包含 messages 和 image 的 payload 格式
        $payload = [
            'model' => $this->model ?? 'agnes-vision',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $this->getPrompt()
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => 'data:image/jpeg;base64,' . $imageBase64
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 1024
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

        $responseData = json_decode($response, true);

        // 如果 API 返回的是标准的 Chat Completion 结构，尝试提取文本并解析为 JSON
        $content = $responseData['choices'][0]['message']['content'] ?? $responseData['result'] ?? null;

        if (is_string($content)) {
            // 清理可能包含的 markdown 代码块标记
            $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
            return json_decode($cleanJson, true);
        }

        return is_array($content) ? $content : null;
    }
}