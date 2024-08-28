<?php

declare(strict_types=1);

namespace App\Controller\Telegram;

use App\Dto\Telegram\Bot\BotRequestData;
use App\Factory\Telegram\Bot\BotFactorySelector;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetWebHookController extends AbstractController
{
    public function __construct(
        private readonly BotFactorySelector $botFactorySelector
    ) {}

    /**
     * @throws TelegramException
     */
    #[Route('/webhook', name: 'getWebhook', methods: ['POST', 'GET'])]
    public function index(Request $request, BotRequestData $botData): Response
    {
        try {
            $botFactory = $this->botFactorySelector->getFactory($request, $botData);
            $telegram   = $botFactory->createBot();

            $telegram->useGetUpdatesWithoutDatabase();
            $telegram->addCommandsPath($botFactory->getCommandsPath());

            $jsonData = $request->getContent();

            if (empty($jsonData)) {
                throw new TelegramException('Input is empty! The webhook must not be called manually, only by Telegram.');
            }

            $telegram->setCustomInput($jsonData);
            $telegram->handle();

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (TelegramException $telegramException) {
            throw new TelegramException($telegramException->getMessage());
        }
    }
}
