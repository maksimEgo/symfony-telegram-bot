<?php

namespace App\State\Telegram;

use Longman\TelegramBot\Entities\Update;

interface StateInterface
{
    public function handle(Update $update);
}
