<?php

namespace App\Action\Combat;

use App\Action\ActionInterface;
use App\Dispatcher\EventDispatcher;
use App\Model\Character;
use App\Model\Event;
use Exception;

class AttackAction implements ActionInterface
{
    /**
     * @throws Exception
     */
    public function execute(Character $actor, ?Character $target, array $params): array
    {

        $actorModifierBonus = $actor->getModifierBonus($params['weaponToUse']['statistic']);
        $total = $params['attackRoll'] + $actorModifierBonus + $actor->getProficiencyBonus();
        $hit = $total >= $target->getArmorClass();

        if ($hit) {
            $dmg = $params['damageRoll'] + $actorModifierBonus;
            $msg = "{$actor->getName()} touche {$target->getName()} (jet $total vs CA {$target->getArmorClass()}) pour $dmg dégâts.";
            $target->setHp($target->getHp() - $dmg);
        } else {
            $msg = "{$actor->getName()} rate {$target->getName()} (jet $total vs CA {$target->getArmorClass()})";
        }

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
