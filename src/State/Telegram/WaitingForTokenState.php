<?php

namespace App\State\Telegram;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use App\Service\User\UserSessionService;

class WaitingForTokenState extends AbstractState implements StateInterface
{
    public function __construct(
        private readonly UserSessionService $userSession
    ) {}

    public function handle(Update $update): ServerResponse
    {
        $message = $update->getMessage();
        $chatId  = $message->getChat()->getId();
        $text    = $message->getText();

        $reply    = $this->reply;
        $buttons  = $this->buttons;
        $nextStep = $this->nextStep;

        if ($nextStep !== null) {
            $this->userSession->setData($chatId, 'state', $nextStep);
        }

        if ($text) {
            $this->userSession->setData($chatId, 'token', $text);
        }
        
        if ($buttons) {
            $keyboard = new Keyboard(
                $buttons
            );

            $keyboard = $keyboard->setResizeKeyboard(true)
                ->setOneTimeKeyboard(true)
                ->setSelective(false);
        }

        return Request::sendMessage([
            'chat_id'      => $chatId,
            'text'         => $reply,
            'reply_markup' => $keyboard ?? null,
        ]);
    }
}
