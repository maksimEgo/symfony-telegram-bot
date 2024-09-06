<?php

namespace App\Factory\Telegram\Interfaces;

use App\Entity\Telegram\InterfaceEntity;

interface TelegramUIInterface
{
    public function createInterface(string $name): InterfaceEntity;
    public function getMessageInterface(string $name): string;
    public function getButtonsInterface(string $name): ?array;
}
