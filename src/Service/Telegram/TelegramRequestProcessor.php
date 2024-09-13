<?php

namespace App\Service\Telegram;

use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\Request;

class TelegramRequestProcessor implements TelegramRequestProcessorInterface
{

    /**
     * @throws TelegramException
     * @throws \JsonException
     */
    public function processRequest(Request $request): Update
    {
        $jsonData = $request->getContent();

        if (empty($jsonData)) {
            throw new TelegramException('Input is empty! The webhook must not be called manually, only by Telegram.');
        }

        $update  = json_decode($jsonData, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);

        return new Update($update);
    }
}