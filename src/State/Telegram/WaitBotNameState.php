<?php

namespace App\State\Telegram;

use App\Service\User\UserSessionService;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;

class WaitBotNameState extends AbstractState implements StateInterface
{
    public function __construct(
        private readonly UserSessionService $userSession
    ) {}

    public function handle(Update $update): ServerResponse
    {
        $message = $update->getMessage();
        $chatId  = $message->getChat()->getId();
        $reply   = $this->reply;

        $token = $this->userSession->getData($chatId, 'token');
        $name  = $message->getText();

        $buttons = [
            ['text' => $name, 'url' => 'https://t.me/' . $name],
        ];

        if ($buttons) {
            $keyboard = new InlineKeyboard($buttons);

            $keyboard->addRow($buttons);
        }

        return Request::sendMessage([
            'chat_id'      => $chatId,
            'text'         => $reply,
            'reply_markup' => $keyboard ?? null,
        ]);
    }
}