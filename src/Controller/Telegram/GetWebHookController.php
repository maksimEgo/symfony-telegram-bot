<?php

declare(strict_types=1);

namespace App\Controller\Telegram;

use App\Dto\Telegram\Bot\BotRequestData;
use App\Factory\Telegram\Bot\BotFactorySelector;
use App\Factory\Telegram\State\StateFactory;
use App\Service\Telegram\WebhookService;
use App\Service\User\UserSessionService;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use JsonException;

class GetWebHookController extends AbstractController
{
    public function __construct(
        private readonly BotFactorySelector $botFactorySelector,
        private readonly WebhookService $webhookService,
        private readonly UserSessionService $userSession,
        private readonly StateFactory $stateFactory
    ) {}

    /**
     * @throws TelegramException|JsonException
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

            [$commandName, $interfaceName] = $this->webhookService->getCommandNaneAndInterfaceName($jsonData);

            $update  = json_decode($jsonData, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);
            $chatId  = $update['message']['chat']['id'];
            $botData = $botFactory->getBot();

            if ($chatId) {
                $state = $this->userSession->getState($chatId);
                if ($state) {
                    $stateHandler = $this->stateFactory->createStateHandler($state);
                    $stateData = $this->webhookService->getCommandData($state, $botData);
                    $stateHandler?->setConfig($stateData)->initialize()->handle(new Update($update));
                    unset($state, $stateData);
                    return new Response(content: '', status: Response::HTTP_NO_CONTENT);
                }
            }
            $commandData = $this->webhookService->getCommandData($interfaceName, $botData);
            $telegram->setCommandConfig($commandName, $commandData);

            $telegram->setCustomInput($jsonData);
            $telegram->handle();
            unset($commandName, $interfaceName, $jsonData, $dataConfig, $update, $chatId, $commandData);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (TelegramException $telegramException) {
            throw new TelegramException($telegramException->getMessage());
        } catch (JsonException $jsonException) {
            throw new JsonException($jsonException->getMessage());
        }
    }
}
