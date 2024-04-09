<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\MessageController;
use App\Entity\Message;
use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;
    
    function test_list_with_results(): void
    {
        $request = new Request();

        $message1 = new Message();
        $message1->setUuid('uuid1');
        $message1->setText('text1');
        $message1->setStatus('sent');
        $message2 = new Message();
        $message2->setUuid('uuid2');
        $message2->setText('text2');
        $message2->setStatus('sent');

        $messages = $this->createMock(MessageRepository::class);
        $messages->expects(self::once())
            ->method('by')
            ->willReturn([$message1, $message2]);

        $testObject = new MessageController();
        $result = $testObject->list($request, $messages);

        /** @var string $rawContent */
        $rawContent = $result->getContent();

        /** @var array<string, array<int, array<string, string>>> $content */
        $content = json_decode($rawContent, true);

        $resultMessages = $content['messages'];

        $message1 = $resultMessages[0];
        $message2 = $resultMessages[1];

        $this->assertCount(2, $resultMessages);
        $this->assertSame('sent', $message1['status']);
        $this->assertSame('uuid2', $message2['uuid']);
    }

    function test_list_empty_results(): void
    {
        $request = new Request();

        $messages = $this->createMock(MessageRepository::class);
        $messages->expects(self::once())
            ->method('by')
            ->willReturn([]);

        $testObject = new MessageController();
        $result = $testObject->list($request, $messages);

        /** @var string $rawContent */
        $rawContent = $result->getContent();

        /** @var array<string, array<int, array<string, string>>> $content */
        $content = json_decode($rawContent, true);

        $resultMessages = $content['messages'];

        $this->assertCount(0, $resultMessages);
    }

    function test_list_success(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages', [
            'status' => 'sent',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    function test_list_invalid_status(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages', [
            'status' => 'something',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    function test_list_invalid_method_type(): void
    {
        $client = static::createClient();
        $client->request('POST', '/messages', [
            'status' => 'sent',
        ]);

        $this->assertResponseStatusCodeSame(405);
    }
    
    function test_that_it_sends_a_message(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send', [
            'text' => 'Hello World',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}
