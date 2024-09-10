<?php

declare(strict_types=1);

namespace App\Factory\Telegram\Bot;

use App\Dto\Telegram\Bot\BotRequestData;
use App\Entity\Telegram\Bot;
use App\Service\Telegram\BotService;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

final class ShopBotFactory implements BotFactoryInterface
{

    private const string TELEGRAM_COMMAND_PATH = __DIR__ . '/../../../Commands/ShopBot/';

    public function __construct(
        private readonly BotRequestData $botData,
        private readonly BotService $botService
    ) {}

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

    /**
     * @return Bot
     */
    public function getBot(): Bot
    {
        return $this->botService->getBotByName($this->botData->botName);
    }
}
