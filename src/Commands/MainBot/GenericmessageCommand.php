<?php

declare(strict_types=1);

namespace App\Commands\MainBot;

use App\Service\User\UserSessionService;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'Genericmessage';

    protected $description = 'Handle all text messages that are not commands';

    protected $version = '1.0.0';

    protected UserSessionService $userSessionService;

    /**
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chatId  = $message->getChat()->getId();

        $this->userSessionService = $this->getConfig('sessionService');

        $reply    = $this->getConfig('reply') ?? 'Unknown command';
        $buttons  = $this->getConfig('buttons');
        $nextStep = $this->getConfig('nextStep');

        if ($nextStep !== null) {
            $this->userSessionService->setState($chatId, $nextStep);
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
