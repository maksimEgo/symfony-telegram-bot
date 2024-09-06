<?php

namespace App\Factory\Telegram\Interfaces;

use App\Entity\Telegram\InterfaceEntity;
use App\Service\Telegram\InterfaceService;

class UIInterfaceFactory implements TelegramUIInterface
{
    public function __construct(
        private readonly InterfaceService $interfaceService
    ) {}

    public function createInterface(string $name): InterfaceEntity
    {
        return $this->interfaceService->getInterfaceByName($name);
    }

    public function getMessageInterface(string $name): string
    {
        $interface = $this->createInterface($name);

        return $interface->getMessage()->getText();
    }

    public function getButtonsInterface(string $name): ?array
    {
        $interface = $this->createInterface($name);

        return $interface->getKeyboard()?->getButtons();
    }
}
