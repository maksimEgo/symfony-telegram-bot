<?php

namespace App\Service\Telegram;

use App\Entity\Telegram\InterfaceEntity;
use App\Repository\Telegram\InterfaceEntityRepository;

class InterfaceService
{
    public function __construct(
        private readonly InterfaceEntityRepository $interfaceEntityRepository,
    ) {}

    public function getInterfaceByName(string $name): ?InterfaceEntity
    {
        return $this->interfaceEntityRepository->getByName($name);
    }
}
