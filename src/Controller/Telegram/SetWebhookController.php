<?php

declare(strict_types=1);

namespace App\Controller\Telegram;

use App\Dto\Telegram\Bot\BotRequestData;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SetWebhookController extends AbstractController
{
    /**
     * @param BotRequestData $botData
     * @param ValidatorInterface $validator
     * @param string $webhookUrl
     * @return Response
     * @throws TelegramException
     */
    #[Route('/setWebhook', name: 'appSetWebhook', methods: ['GET'])]
    public function index(
        BotRequestData $botData,
        ValidatorInterface $validator,
        #[Autowire(value: '%env(WEBHOOK_URL)%')] string $webhookUrl
    ): Response {
        $violations = $validator->validate($botData);

        if (count($violations) > 0) {
            $errorsString = (string) $violations;

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        try {
            $telegram = new Telegram($botData->botToken, $botData->botName);

            $webhook = $telegram->setWebhook(
                $webhookUrl
                . '/webhook?bot_token='
                . $botData->botToken
                . '&bot_name='
                . $botData->botName
            );

            if ($webhook->isOk()) {
                return new Response('WebHook set', Response::HTTP_OK);
            }

            return new Response('WebHook not set', Response::HTTP_BAD_REQUEST);
        } catch (TelegramException $telegramException) {
            throw new TelegramException($telegramException->getMessage());
        }
    }
}
