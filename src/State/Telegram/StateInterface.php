<?php

namespace App\State\Telegram;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;

interface StateInterface
{
    public function handle(Update $update): ServerResponse;
    public function setConfig(array $config): self;
    public function getConfig(): ?array;
}
