<?php

namespace App\State\Telegram;

use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use App\Service\User\UserSessionService;

class WaitingForTokenState extends AbstractState implements StateInterface
{
    public function __construct(
        private readonly UserSessionService $userSession
    ) {}

    public function handle(Update $update): void
    {
        $message = $update->getMessage();
        $chatId  = $message->getChat()->getId();
        $text    = $message->getText();

        $this->userSession->setState($chatId, 'ready');
        $responseText = 'Token is save.';

        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $responseText
        ]);
    }
}
