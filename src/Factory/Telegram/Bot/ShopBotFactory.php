<?php

declare(strict_types=1);

namespace App\Factory\Telegram\Bot;

use App\Dto\Telegram\Bot\BotRequestData;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

final class ShopBotFactory implements BotFactoryInterface
{

    private const string TELEGRAM_COMMAND_PATH = __DIR__ . '/../../../Commands/ShopBot/';

    private BotRequestData $botData;

    public function __construct(BotRequestData $botData)
    {
        $this->botData = $botData;
    }

    /**
     * @throws TelegramException
     */
    public function createBot(): Telegram
    {
        return new Telegram($this->botData->botToken, $this->botData->botName);
    }

    public function getCommandsPath(): string
    {
        return self::TELEGRAM_COMMAND_PATH;
    }
}
