<?php

declare(strict_types=1);

namespace App\ValueResolver\Telegram\Bot;

use App\Dto\Telegram\Bot\BotRequestData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class BotDataResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (BotRequestData::class !== $argument->getType()) {
            return [];
        }

        $botData = new BotRequestData();
        $botData->botName = $request->query->get('bot_name');
        $botData->botToken = $request->query->get('bot_token');

        return [$botData];
    }
}
