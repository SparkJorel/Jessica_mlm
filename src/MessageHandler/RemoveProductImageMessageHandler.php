<?php

namespace App\MessageHandler;

use App\Message\RemoveProductImageMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveProductImageMessageHandler implements MessageHandlerInterface
{
    public function __invoke(RemoveProductImageMessage $message)
    {
        $filename = $message->getFilename();

        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}
