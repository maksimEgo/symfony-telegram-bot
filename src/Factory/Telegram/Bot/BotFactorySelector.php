<?php

declare(strict_types=1);

namespace App\Factory\Telegram\Bot;

use App\Dto\Telegram\Bot\BotRequestData;
use Symfony\Component\HttpFoundation\Request;

readonly class BotFactorySelector
{
    public function __construct(
        private MainBotFactory $mainBotFactory,
        private ShopBotFactory $shopBotFactory
    ) {}

    public function getFactory(Request $request, BotRequestData $botRequestData): BotFactoryInterface
    {
        if (
            empty($botRequestData->botToken)
            || $request->isMethod(Request::METHOD_POST)
        ) {
            return $this->mainBotFactory;
        }

        return $this->shopBotFactory;
    }
}
