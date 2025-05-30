<?php
namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Dispatcher\EventDispatcher;
use App\Model\Event;
use App\Model\Character;

class MoveAction implements ActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $from = 0; //todo: faire système de position;
        $to   = $from + $params['distance'];
        $msg = "{$actor->getName()} se déplace de {$from} à {$to}.";

        $evt = new Event($msg);
        EventDispatcher::getInstance()->dispatch($evt);

        return [
            'events' => [$evt->getMessage()],
            'result' => ['newPosition'=>$to]
        ];
    }
}
