<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Dispatcher\EventDispatcher;
use App\Model\Character;
use App\Model\Event;
use Exception;

class UseItemAction implements ActionInterface
{
    /**
     * @throws Exception
     */
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $itemToUse = $params['itemToUse'];
        $msg = "Le joueur utilise « $itemToUse » .";

        $actor->consumeItem($itemToUse);

        $evt = new Event($msg);
        EventDispatcher::getInstance()->dispatch($evt);

        return [
            'events' => [$evt->getMessage()],
            'result' => [
                'actor' => $actor->toArray(),
                'target' => $target->toArray(),
            ]
        ];
    }
}