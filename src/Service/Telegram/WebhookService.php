<?php

namespace App\Service\Telegram;

class WebhookService
{
    /**
     * @throws \JsonException
     */
    public function getCommandNaneAndInterfaceName(string $jsonData): array
    {
        $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
        $command = $data['message']['text'];

        if (str_starts_with($command, '/')) {
            $interfaceName = ltrim($command, '/');
            $commandName = ltrim($command, '/');

            return [$commandName, $interfaceName];
        }
        $interfaceName = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $command));
        $commandName = 'Genericmessage';

        return [$commandName, $interfaceName];
    }
}
