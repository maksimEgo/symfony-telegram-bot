<?php

namespace App\Service\Telegram;

use Longman\TelegramBot\Entities\Update;
use Symfony\Component\HttpFoundation\Request;

interface TelegramRequestProcessorInterface
{
    public function processRequest(Request $request): Update;
}