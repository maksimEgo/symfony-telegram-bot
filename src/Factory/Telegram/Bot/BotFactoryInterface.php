<?php

declare(strict_types=1);

namespace App\Factory\Telegram\Bot;

use App\Entity\Telegram\Bot;
use Longman\TelegramBot\Telegram;

interface BotFactoryInterface
{
    /**
     * @return Telegram
     */
    public function createBot(): Telegram;

    /**
     * @return string
     */
    public function getCommandsPath(): string;

    /**
     * @return Bot
     */
    public function getBot(): Bot;
}
