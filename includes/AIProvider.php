<?php
declare(strict_types=1);

abstract class AIProvider {
    protected string $apiKey;
    protected string $model;
    protected array $fallbackModels;
    protected int $timeout;

    public function __construct(string $apiKey, string $model, array $fallbackModels = [], int $timeout = 3000) {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->fallbackModels = $fallbackModels;
        $this->timeout = $timeout;
    }

    abstract public function recognize(string $imageBase64): ?array;

    protected function getPrompt(): string {
        return "Analyze this image and return JSON strictly with format: " .
               '{"objectLabel": "string", "productName": "string", "manufacturer": "string", "specification": "string", "description": "string", "boundingBox": {"ymin": int, "xmin": int, "ymax": int, "xmax": int}, "confidence": float}. ' .
               "Coordinates 0-1000. Do NOT invent model numbers without clear evidence.";
    }
}