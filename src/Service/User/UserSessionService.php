<?php

namespace App\Service\User;

use Predis\Client;

class UserSessionService
{
    private Client $redis;
    private const int SESSION_TTL = 1800;

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

    public function getState(int $chatId): string|bool
    {
        try {
            $state = $this->redis->get("user_state:$chatId");

            if ($state !== null) {
                $this->redis->del(["user_state:$chatId"]);
                return $state;
            }

            return false;
        } catch (\Exception $e) {
            throw new \RuntimeException('The error occurred while retrieving the user status: ' . $e->getMessage());
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
            throw new \RuntimeException('The error occurred while setting the user status: ' . $e->getMessage());
        }
    }
}
