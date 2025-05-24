<?php

namespace App\Action\Combat;

use App\Action\ActionInterface;
use App\Dispatcher\EventDispatcher;
use App\Model\Character;
use App\Model\Event;
use InvalidArgumentException;

class AttackAction implements ActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        if (!$target) {
            throw new InvalidArgumentException("Cible requise pour une attaque");
        }

        $total = $params['roll'] + $params['attack_mod'];
        $hit = $total >= $target->getArmorClass();

        if ($hit) {
            $dmg = $params['damage_roll'] + $params['damage_mod'];
            $msg = "{$actor->getName()} touche {$target->getName()} (jet {$total} vs CA {$target->getArmorClass()}) pour {$dmg} dégâts.";
            $result = ['hit' => true, 'damage' => $dmg];
        } else {
            $msg = "{$actor->getName()} rate {$target->getName()} (jet {$total} vs CA {$target->getArmorClass()})";
            $result = ['hit' => false];
        }

        $evt = new Event($msg);
        EventDispatcher::getInstance()->dispatch($evt);

        return [
            'events' => [$evt->getMessage()],
            'result' => $result
        ];
    }
}
