<?php

namespace App\Service\User;

use Predis\Client;

class UserSessionService
{
    private Client $redis;
    private const SESSION_TTL = 1800;

    public function __construct(
        string $redisHost = 'redis',
        int $redisPort = 6379
    ) {
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host'   => $redisHost,
            'port'   => $redisPort,
        ]);
    }

    public function getState(int $chatId): ?string
    {
        try {
            $state = $this->redis->get("user_state:$chatId");

            if ($state !== false) {
                $this->redis->del(["user_state:$chatId"]);
                return $state;
            }

            return null;
        } catch (\Exception $e) {
            throw new \RuntimeException('Ошибка при получении состояния пользователя: ' . $e->getMessage());
        }
    }

    public function setState(int $chatId, ?string $state): void
    {
        try {
            if ($state !== null) {
                $this->redis->setex("user_state:$chatId", self::SESSION_TTL, $state);
            } else {
                $this->redis->del(["user_state:$chatId"]);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Ошибка при установке состояния пользователя: ' . $e->getMessage());
        }
    }
}
