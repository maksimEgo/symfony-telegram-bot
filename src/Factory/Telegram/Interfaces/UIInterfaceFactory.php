<?php

namespace App\Factory\Telegram\Interfaces;

use App\Entity\Telegram\InterfaceEntity;
use App\Service\Telegram\InterfaceService;

readonly class UIInterfaceFactory implements TelegramUIInterface
{
    public function __construct(
        private InterfaceService $interfaceService
    ) {}

    public function createInterface(string $name): ?InterfaceEntity
    {
        $interface = $this->interfaceService->getInterfaceByName($name);

        if ($interface instanceof InterfaceEntity) {
            return $interface;
        }

        return null;
    }

    public function getMessageInterface(string $name): ?string
    {
        $interface = $this->createInterface($name);

        if ($interface instanceof InterfaceEntity) {
            return $interface->getMessage()->getText();
        }

        return null;
    }

    public function getButtonsInterface(string $name): ?array
    {
        $interface = $this->createInterface($name);

        if ($interface instanceof InterfaceEntity) {
            return $interface->getKeyboard()?->getButtons();
        }

        return null;
    }

    public function getNextStep(string $name): ?string
    {
        $interface = $this->createInterface($name);

        if ($interface instanceof InterfaceEntity) {
            return $interface->getNextStep();
        }

        return null;
    }

}
