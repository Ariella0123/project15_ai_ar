<?php
declare(strict_types=1);

require_once __DIR__ . '/AppConfig.php';
require_once __DIR__ . '/GeminiVision.php';
require_once __DIR__ . '/AgnesVision.php';

class ObjectRecognizer {
    public function processImage(string $imageBase64): array {
        $config = AppConfig::get();
        $provider = $config['ai_provider'] ?? 'auto';
        $result = null;
        $usedProvider = '';

        if ($provider === 'auto' || $provider === 'gemini') {
            $gemini = new GeminiVision(
                $config['gemini_api_key'],
                $config['gemini_model'],
                $config['gemini_fallback_models'] ?? [],
                $config['request_timeout']
            );
            $result = $gemini->recognize($imageBase64);
            if ($result) $usedProvider = 'gemini';
        }

        if (!$result && ($provider === 'auto' || $provider === 'agnes')) {
            $agnes = new AgnesVision(
                $config['agnes_api_key'],
                $config['agnes_model'],
                $config['agnes_fallback_models'] ?? [],
                $config['request_timeout']
            );
            $result = $agnes->recognize($imageBase64);
            if ($result) $usedProvider = 'agnes';
        }

        
        // return ['ok' => true, 'data' => $result, 'provider' => $usedProvider];
        if (!$result) {
            return ['ok' => false, 'error' => 'Unable to identify object using active AI providers.'];
        }

        $validated = $this->validateAndNormalize($result);
        $validated['provider'] = $usedProvider;

        return ['ok' => true, 'data' => $validated];
    }

    private function validateAndNormalize(array $data): array {
        
        return [
            'objectLabel' => $data['objectLabel'] ?? 'Unknown Object',
            'productName' => $data['productName'] ?? 'Unknown Product',
            'manufacturer' => $data['manufacturer'] ?? 'Generic',
            'specification' => $data['specification'] ?? 'N/A',
            'description' => $data['description'] ?? 'No description available.',
            'boundingBox' => [
                'ymin' => min(1000, max(0, (int)($data['boundingBox']['ymin'] ?? 100))),
                'xmin' => min(1000, max(0, (int)($data['boundingBox']['xmin'] ?? 100))),
                'ymax' => min(1000, max(0, (int)($data['boundingBox']['ymax'] ?? 900))),
                'xmax' => min(1000, max(0, (int)($data['boundingBox']['xmax'] ?? 900)))
            ],
            'confidence' => min(1.0, max(0.0, (float)($data['confidence'] ?? 0.8)))
        ];
    }
}