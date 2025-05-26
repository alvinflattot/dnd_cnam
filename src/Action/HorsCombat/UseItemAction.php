<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;
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

        return [
            'events' => [$msg],
            'result' => [
                'actor' => $actor->toArray(),
                'target' => $target->toArray(),
            ]
        ];
    }
}