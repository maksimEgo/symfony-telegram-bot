<?php

namespace App\State\Telegram;

use App\Entity\Telegram\Bot;
use App\Service\User\UserSessionService;

abstract class AbstractState
{
    private   ?array  $config;
    protected string $reply;
    protected ?array  $buttons;
    protected ?string $nextStep;
    protected ?Bot    $botData;
    protected ?UserSessionService $sessionService;

    public function setConfig(array $config): StateInterface
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }

    protected function map(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function initialize(): StateInterface
    {
        $this->map($this->config);

        return $this;
    }
}
