<?php

declare(strict_types=1);

namespace App\Factory\Telegram\Bot;

use App\Entity\Telegram\Bot;
use Longman\TelegramBot\Telegram;

interface BotFactoryInterface
{
    public function createBot(): Telegram;
    public function getCommandsPath(): string;
    public function getBot(): Bot;
}
