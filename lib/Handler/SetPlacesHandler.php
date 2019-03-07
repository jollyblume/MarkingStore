<?php

namespace JBJ\Workflow\MarkingStore\Handler;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

use JBJ\Workflow\MarkingStore\Message\GetPlaces;

class SetPlacesHandler extends MessageHandlerInterface
{
    public function __invoke(GetPlaces $message)
    {
    }
}
