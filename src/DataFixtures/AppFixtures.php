<?php

namespace App\DataFixtures;

use App\Entity\Telegram\Bot;
use App\Entity\Telegram\InterfaceEntity;
use App\Entity\Telegram\Keyboard;
use App\Entity\Telegram\Message;
use App\Enum\Telegram\BotType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $startMessage = new Message();
        $startMessage->setText('Hello! Welcome to the Main bot. Please choose an option:');
        $manager->persist($startMessage);

        $startKeyboard = new Keyboard();
        $startKeyboard->setButtons(['🚀 Create Bot', '🗝 Managing Bots']);
        $manager->persist($startKeyboard);

        $startInterface = new InterfaceEntity();
        $startInterface->setName('start');
        $startInterface->setMessage($startMessage);
        $startInterface->setKeyboard($startKeyboard);
        $manager->persist($startInterface);

        $createMessage = new Message();
        $createMessage->setText('Please send your bot name.');
        $manager->persist($createMessage);

        $createInterface = new InterfaceEntity();
        $createInterface->setName('Create Bot');
        $createInterface->setMessage($createMessage);
        $createInterface->setNextStep('WaitingForToken');
        $manager->persist($createInterface);

        $tokenMessage = new Message();
        $tokenMessage->setText('Please send token bot name.');
        $manager->persist($tokenMessage);

        $waitingForTokenInterface = new InterfaceEntity();
        $waitingForTokenInterface->setName('WaitingForToken');
        $waitingForTokenInterface->setMessage($tokenMessage);
        $waitingForTokenInterface->setNextStep('WaitBotName');
        $manager->persist($waitingForTokenInterface);

        $mainBot = new Bot();
        $mainBot->setName(getenv('TELEGRAM_BOT_NAME'));
        $mainBot->setToken(getenv('TELEGRAM_BOT_TOKEN'));
        $mainBot->setBotType(BotType::MAIN);
        $manager->persist($mainBot);

        $manager->flush();
    }
}
