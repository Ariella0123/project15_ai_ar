<?php
declare(strict_types=1);

require_once __DIR__ . '/AIProvider.php';

class AgnesVision extends AIProvider {
    public function recognize(string $imageBase64): ?array {
        if (empty($this->apiKey)) return null;
    
        
        $url = "https://apihub.agnes-ai.com/v1/chat/completions"; 

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
            'max_tokens' => 2048
        ];
        
    
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            // 如果你在本地开发环境中遇到 SSL 问题，可以暂时禁用验证
            
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => $this->timeout
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 捕获 cURL 底层错误
        

        curl_close($ch);
        // 如果 HTTP 状态码不是 200 或没有响应，返回 null
        
        if ($httpCode !== 200 || !$response) {
            // 你可以在这里临时加上：error_log("API Error: HTTP $httpCode - $response");
            return null;
        }
        

        $responseData = json_decode($response, true);
        

        // 提取返回的内容
        $content = $responseData['choices'][0]['message']['content'] ?? $responseData['result'] ?? null;

        if (is_string($content)) {
            // 清理 markdown 标签并转为数组
            $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
            return json_decode($cleanJson, true);
        }

        return is_array($content) ? $content : null;
    }
}