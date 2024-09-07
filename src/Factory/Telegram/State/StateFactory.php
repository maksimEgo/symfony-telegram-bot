<?php

namespace App\Factory\Telegram\State;

use App\State\Telegram\StateInterface;
use App\Service\User\UserSessionService;
use InvalidArgumentException;

readonly class StateFactory
{
    public function __construct(
        private UserSessionService $userSessionService,
    ) {}

    public function createStateHandler(string $state): StateInterface
    {
        $stateClass = 'App\\State\\Telegram\\' . $state . 'State';

        if (class_exists($stateClass)) {
            return new $stateClass($this->userSessionService);
        }

        throw new InvalidArgumentException("State [$state] does not exist.");
    }
}