<?php

namespace App\Service\User;

use Predis\Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserSessionService
{
    private Client $redis;
    private const int SESSION_TTL = 1800;

    public function __construct(
        #[Autowire(value: '%env(REDIS_HOST)%')]
        string $redisHost,

        #[Autowire(value: '%env(REDIS_PORT)%')]
        int $redisPort
    ) {
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host'   => $redisHost,
            'port'   => $redisPort,
        ]);
    }

    public function setData(int $chatId, string $key, mixed $value): void
    {
        try {
            $sessionKey = "user_session:$chatId";

            $sessionData = $this->getSessionData($chatId);

            $sessionData[$key] = $value;

            $this->redis->setex($sessionKey, self::SESSION_TTL, json_encode($sessionData));
        } catch (\Exception $e) {
            throw new \RuntimeException('Error installing user data: ' . $e->getMessage());
        }
    }

    public function getData(int $chatId, string $key): ?string
    {
        try {
            $sessionData = $this->getSessionData($chatId);
            $this->removeData($chatId, $key);

            return $sessionData[$key] ?? null;
        } catch (\Exception $e) {
            throw new \RuntimeException('Error retrieving user data: ' . $e->getMessage());
        }
    }

    private function removeData(int $chatId, string $key): void
    {
        try {
            $sessionKey = "user_session:$chatId";

            $sessionData = $this->getSessionData($chatId);

            unset($sessionData[$key]);

            $this->redis->setex($sessionKey, self::SESSION_TTL, json_encode($sessionData));
        } catch (\Exception $e) {
            throw new \RuntimeException('Ошибка при удалении данных пользователя: ' . $e->getMessage());
        }
    }

    private function getSessionData(int $chatId): array
    {
        try {
            $sessionKey = "user_session:$chatId";
            $sessionData = $this->redis->get($sessionKey);

            return $sessionData ? json_decode($sessionData, true) : [];
        } catch (\Exception $e) {
            throw new \RuntimeException('Ошибка при получении данных сессии пользователя: ' . $e->getMessage());
        }
    }

    public function clearSession(int $chatId): void
    {
        try {
            $this->redis->del(["user_session:$chatId"]);
        } catch (\Exception $e) {
            throw new \RuntimeException('Ошибка при очистке сессии пользователя: ' . $e->getMessage());
        }
    }
}
