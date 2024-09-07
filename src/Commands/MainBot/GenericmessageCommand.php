<?php

declare(strict_types=1);

namespace App\Commands\MainBot;

use App\Service\User\UserSessionService;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'Genericmessage';

    protected $description = 'Handle all text messages that are not commands';

    protected $version = '1.0.0';

    protected UserSessionService $userSessionService;

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chatId  = $message->getChat()->getId();
        $text    = $message->getText(true);

        $this->userSessionService = $this->getConfig('sessionService');

        $reply    = $this->getConfig('reply');
        $buttons = $this->getConfig('buttons');

        if ($text === '🚀 Create Bot') {
            $this->userSessionService->setState($chatId, 'WaitingForToken');
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
