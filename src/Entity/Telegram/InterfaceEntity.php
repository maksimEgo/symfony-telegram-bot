<?php

namespace App\Entity\Telegram;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'interfaces')]
class InterfaceEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private readonly int $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $name;

    #[ORM\OneToOne(targetEntity: Message::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id', nullable: false)]
    private Message $message;

    #[ORM\OneToOne(targetEntity: Keyboard::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'keyboard_id', referencedColumnName: 'id', nullable: true)]
    private ?Keyboard $keyboard = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nextStep = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getKeyboard(): ?Keyboard
    {
        return $this->keyboard;
    }

    public function setKeyboard(?Keyboard $keyboard): self
    {
        $this->keyboard = $keyboard;
        return $this;
    }

    public function getNextStep(): ?string
    {
        return $this->nextStep;
    }

    public function setNextStep(string $nextStep): void
    {
        $this->nextStep = $nextStep;
    }
}
