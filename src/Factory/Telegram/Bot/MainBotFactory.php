<?php

declare(strict_types=1);

namespace App\Factory\Telegram\Bot;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class MainBotFactory implements BotFactoryInterface
{
    private const string TELEGRAM_COMMAND_PATH = __DIR__ . '/../../../Commands/MainBot/';

    public function __construct(
        #[Autowire('%env(TELEGRAM_BOT_TOKEN)%')]
        private readonly string $botToken,

        #[Autowire('%env(TELEGRAM_BOT_NAME)%')]
        private readonly string $botName
    ) {}

    /**
     * @throws TelegramException
     */
    public function createBot(): Telegram
    {
       return new Telegram($this->botToken, $this->botName);
    }

    public function getCommandsPath(): string
    {
        return self::TELEGRAM_COMMAND_PATH;
    }
}
