<?php

declare(strict_types=1);

namespace App\Controller\Telegram;

use App\Dto\Telegram\Bot\BotRequestData;
use App\Factory\Telegram\Bot\BotFactorySelector;
use App\Factory\Telegram\State\StateFactory;
use App\Service\Telegram\TelegramRequestProcessorInterface;
use App\Service\Telegram\WebhookService;
use App\Service\User\UserSessionService;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use JsonException;

class GetWebHookController extends AbstractController
{
    public function __construct(
        private readonly TelegramRequestProcessorInterface $telegramRequestProcessor,
        private readonly UserSessionService $userSession,
        private readonly StateFactory $stateFactory,
        private readonly BotFactorySelector $botFactorySelector,
        private readonly WebhookService $webhookService,
    ) {}

    /**
     * @throws TelegramException|JsonException
     */
    #[Route('/webhook', name: 'getWebhook', methods: ['POST', 'GET'])]
    public function index(Request $request, BotRequestData $botData): Response
    {
        try {
            $update = $this->telegramRequestProcessor->processRequest($request);

            $botFactory = $this->botFactorySelector->getFactory($request, $botData);
            $telegram   = $botFactory->createBot();

            $telegram->useGetUpdatesWithoutDatabase();
            $telegram->addCommandsPath($botFactory->getCommandsPath());
            $command = $update->getMessage()->getText();

            [$commandName, $interfaceName] = $this->webhookService->getCommandNaneAndInterfaceName($command);
            $chatId  = $update->getMessage()->getChat()->getId();

            if ($chatId) {
                $state = $this->userSession->getData($chatId, 'state');
                if ($state) {
                    $stateHandler = $this->stateFactory->createStateHandler($state);
                    $stateData = $this->webhookService->getCommandData($state, $botFactory->getBot());
                    $stateHandler?->setConfig($stateData)->initialize()->handle($update);
                    return new Response('', Response::HTTP_NO_CONTENT);
                }
            }
            $commandData = $this->webhookService->getCommandData($interfaceName, $botFactory->getBot());
            $telegram->setCommandConfig($commandName, $commandData);

            $telegram->setCustomInput($request->getContent());
            $telegram->handle();
            unset($commandName, $interfaceName, $update, $chatId, $commandData);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (TelegramException $telegramException) {
            throw new TelegramException($telegramException->getMessage());
        } catch (JsonException $jsonException) {
            throw new JsonException($jsonException->getMessage());
        }
    }
}
