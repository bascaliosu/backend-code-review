<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendMessageHandlerTest extends KernelTestCase
{
    public function test_invoke(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects(self::once())
            ->method('persist');
        $entityManager
            ->expects(self::once())
            ->method('flush');

        $sendMessage = $this->createMock(SendMessage::class);
        $sendMessage->text = 'test text';

        $testObject = new SendMessageHandler($entityManager);

        $testObject->__invoke($sendMessage);
    }
}
