<?php

declare(strict_types=1);

namespace App\Commands\MainBot;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class StartCommand extends UserCommand
{
    protected $name = 'start';

    protected $description = 'Start command main bot';

    protected $usage = '/start';

    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $chatId  = $message->getChat()->getId();

        $text = 'Hello! Welcome to the Main bot. Please choose an option:';

        $keyboard = new Keyboard(
            ['🚀 Create Bot']
        );

        $keyboard = $keyboard->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(false);

        return Request::sendMessage([
            'chat_id'      => $chatId,
            'text'         => $text,
            'reply_markup' => $keyboard,
        ]);
    }
}
