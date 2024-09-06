<?php

declare(strict_types=1);

namespace App\Commands\MainBot;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'Genericmessage';

    protected $description = 'Handle all text messages that are not commands';

    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chatId  = $message->getChat()->getId();
        $text    = $message->getText(true);

        $reply    = $this->getConfig('reply');
        $buttons = $this->getConfig('buttons');

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
