<?php
declare(strict_types=1);

require_once __DIR__ . '/EnvLoader.php';

class AppConfig {
    private static ?array $config = null;

    public static function get(): array {
        if (self::$config === null) {
            // 自动加载根目录下的 .env 文件
            EnvLoader::load(__DIR__ . '/../.env');

            // 优先从环境变量读取，若不存在则回退至配置文件或默认值
            $configFile = __DIR__ . '/../config/config.php';
            $fileConfig = file_exists($configFile) ? require $configFile : [];

            self::$config = [
                'ai_provider' => getenv('AI_PROVIDER') ?: ($fileConfig['ai_provider'] ?? 'auto'),
                'gemini_api_key' => getenv('GEMINI_API_KEY') ?: ($fileConfig['gemini_api_key'] ?? ''),
                'gemini_model' => getenv('GEMINI_MODEL') ?: ($fileConfig['gemini_model'] ?? 'gemini-1.5-flash'),
                'gemini_fallback_models' => $fileConfig['gemini_fallback_models'] ?? ['gemini-1.5-pro'],
                'agnes_api_key' => getenv('AGNES_API_KEY') ?: ($fileConfig['agnes_api_key'] ?? ''),
                'agnes_model' => getenv('AGNES_MODEL') ?: ($fileConfig['agnes_model'] ?? 'agnes-vision-v1'),
                'agnes_fallback_models' => $fileConfig['agnes_fallback_models'] ?? [],
                'request_timeout' => (int)(getenv('REQUEST_TIMEOUT') ?: ($fileConfig['request_timeout'] ?? 30)),
            ];
        }
        return self::$config;
    }
}