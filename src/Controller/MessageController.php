<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @see MessageControllerTest
 */
class MessageController extends AbstractController
{
    #[Route('/messages', methods: ['GET'])]
    public function list(Request $request, MessageRepository $messageRepository): Response
    {
        $status = $request->query->get('status');

        if ($status !== null) {
            $status = (string) $status;
        }

        /**
         * send only parameter to search by to method, not full request
         */
        $rawMessages = $messageRepository->by($status);

        $messages = [];
  
        foreach ($rawMessages as $key => $message) {
            $messages[$key] = [
                'uuid' => $message->getUuid(),
                'text' => $message->getText(),
                'status' => $message->getStatus(),
            ];
        }
        
        return new Response(json_encode([
            'messages' => $messages,
        ], JSON_THROW_ON_ERROR), headers: ['Content-Type' => 'application/json']);
    }

    #[Route('/messages/send', methods: ['GET'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        $text = $request->query->get('text');
        
        if (!$text) {
            return new Response('Text is required', 400);
        }

        $bus->dispatch(new SendMessage((string) $text));
        
        return new Response('Successfully sent', 204);
    }
}
