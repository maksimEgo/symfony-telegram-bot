<?php

declare(strict_types=1);

namespace App\Service\Telegram;

use App\Entity\Telegram\Bot;
use App\Enum\Telegram\BotType;
use App\Repository\Telegram\BotRepository;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class BotService
{
    public function __construct(
        #[Autowire(service: BotRepository::class)]
        private BotRepository $botRepository,
    ) {}

    public function getAllBots(): array
    {
        $bots = $this->botRepository->getAll();

        if (empty($bots)) {
            throw new RuntimeException('No bots found');
        }

        return $bots;
    }

    public function getBotById(int $id): Bot
    {
        $bot = $this->botRepository->getById($id);

        if ($bot === null) {
            throw new RuntimeException('Bot not found by id: ' . $id);
        }

        if (!$bot instanceof Bot) {
            throw new RuntimeException('Invalid bot returned by id: ' . $id);
        }

        return $bot;
    }

    public function getBotByToken(string $token): Bot
    {
        $bot = $this->botRepository->getByToken($token);

        if ($bot === null) {
            throw new RuntimeException('Bot not found by token: ' . $token);
        }

        if (!$bot instanceof Bot) {
            throw new RuntimeException('Invalid bot returned by token: ' . $token);
        }

        return $bot;
    }

    public function getBotByName(string $name): Bot
    {
        $bot = $this->botRepository->getByName($name);

        if ($bot === null) {
            throw new RuntimeException('Bot not found by name: ' . $name);
        }

        if (!$bot instanceof Bot) {
            throw new RuntimeException('Invalid bot returned by name: ' . $name);
        }

        return $bot;
    }

    public function getBotsByType(BotType $type): Bot
    {
        $bot = $this->botRepository->getByBotType($type);

        if ($bot === null) {
            throw new RuntimeException('No bot found by type: ' . $type->value);
        }

        if (!$bot instanceof Bot) {
            throw new RuntimeException('Invalid bot returned by type: ' . $type->value);
        }

        return $bot;
    }

    public function getActiveBots(): array
    {
        $bots = $this->botRepository->findActiveBots();

        if (empty($bots)) {
            throw new RuntimeException('No active bots found');
        }

        return $bots;
    }

    public function getBotsByDataRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $bots = $this->botRepository->findByDateRange($startDate, $endDate);

        if (empty($bots)) {
            throw new RuntimeException('No date range bots found');
        }

        return $bots;
    }

    public function getBotsByNameLike(string $name): array
    {
        $bots = $this->botRepository->findByNameLike($name);

        if (empty($bots)) {
            throw new RuntimeException('No name like bots found');
        }

        return $bots;
    }

    public function getActiveBotsByType(BotType $type): array
    {
        $bots = $this->botRepository->findActiveByType($type);

        if (empty($bots)) {
            throw new RuntimeException('No active bots found by type: ' . $type->value);
        }

        return $bots;
    }

    public function createBot(string $name, string $token, BotType $botType): Bot
    {
        $bot = new Bot();
        $bot->setName($name);
        $bot->setToken($token);
        $bot->setBotType($botType);
        $this->botRepository->save($bot, true);

        return $bot;
    }

    public function updateBot(Bot $bot): void
    {
        $bot->update();
        $this->botRepository->save($bot, true);
    }

    public function deactivateBot(Bot $bot): void
    {
        $bot->setIsActive(false);
        $this->botRepository->save($bot, true);
    }

    public function deleteBot(Bot $bot): void
    {
        $this->botRepository->delete($bot, true);
    }
}
