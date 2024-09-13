<?php

namespace App\Service\Telegram;

use App\Entity\Telegram\Bot;
use App\Factory\Telegram\Interfaces\UIInterfaceFactory;
use App\Service\User\UserSessionService;

class WebhookService
{
    public function __construct(
        private readonly UserSessionService $userSession,
        private readonly UIInterfaceFactory $interfaceFactory
    ) {}

    /**
     * @throws \JsonException
     */
    public function getCommandNaneAndInterfaceName(string $command): array
    {
        if (str_starts_with($command, '/')) {
            $interfaceName = ltrim($command, '/');
            $commandName = ltrim($command, '/');

            return [$commandName, $interfaceName];
        }
        $interfaceName = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $command));
        $commandName = 'Genericmessage';

        return [$commandName, $interfaceName];
    }

    public function getCommandData(string $interfaceName, Bot $bot): array
    {
        return [
            'reply'          => $this->interfaceFactory->getMessageInterface($interfaceName),
            'buttons'        => $this->interfaceFactory->getButtonsInterface($interfaceName),
            'nextStep'       => $this->interfaceFactory->getNextStep($interfaceName),
            'botData'        => $bot,
            'sessionService' => $this->userSession
        ];
    }
}
