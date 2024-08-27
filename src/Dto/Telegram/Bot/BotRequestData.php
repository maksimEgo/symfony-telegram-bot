<?php

declare(strict_types=1);

namespace App\Dto\Telegram\Bot;

use Symfony\Component\Validator\Constraints as Assert;

class BotRequestData
{
    #[Assert\NotBlank(message: 'The bot name should not be blank.')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'The bot name must be at least {{ limit }} characters long.',
        maxMessage: 'The bot name cannot be longer than {{ limit }} characters.'
    )]
    public ?string $botName = null;

    #[Assert\NotBlank(message: 'The bot token should not be blank.')]
    #[Assert\Length(
        min: 40,
        max: 50,
        exactMessage: 'The bot token must be exactly {{ limit }} characters long.'
    )]
    public ?string $botToken = null;
}
