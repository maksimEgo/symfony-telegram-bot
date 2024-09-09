<?php

declare(strict_types=1);

namespace App\Controller\Telegram;

use App\Dto\Telegram\Bot\BotRequestData;
use App\Factory\Telegram\Bot\BotFactorySelector;
use App\Factory\Telegram\Interfaces\UIInterfaceFactory;
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
        private readonly UIInterfaceFactory $interfaceFactory,
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
            $dataConfig = [
                    'reply'          => $this->interfaceFactory->getMessageInterface($interfaceName),
                    'buttons'        => $this->interfaceFactory->getButtonsInterface($interfaceName),
                    'nextStep'       => $this->interfaceFactory->getNextStep($interfaceName),
                    'botData'        => $botFactory->getBot(),
                    'sessionService' => $this->userSession
            ];

            $update = json_decode($jsonData, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);
            $chatId = $update['message']['chat']['id'];

            if ($chatId) {
                $state = $this->userSession->getState($chatId);
                if ($state) {
                    $stateHandler = $this->stateFactory->createStateHandler($state);
                    $stateHandler?->setConfig($dataConfig)->initialize()->handle(new Update($update));
                    return new Response(content: '', status: Response::HTTP_NO_CONTENT);
                }
            }
            $telegram->setCommandConfig($commandName, $dataConfig);

            $telegram->setCustomInput($jsonData);
            $telegram->handle();

            unset($commandName, $interfaceName, $jsonData, $dataConfig, $update, $chatId);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (TelegramException $telegramException) {
            throw new TelegramException($telegramException->getMessage());
        } catch (JsonException $jsonException) {
            throw new JsonException($jsonException->getMessage());
        }
    }
}
