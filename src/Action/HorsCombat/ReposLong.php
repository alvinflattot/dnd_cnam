<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;

class ReposLong implements ActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array
    {
        $staminaGain = $actor->getStaminaMax();
        $hpGain = $actor->getHpMax();
        $events = [];

        $actor->setHp($hpGain);
        $actor->setStamina($staminaGain);

        // Réinitialisation des sorts
        $spells = $actor->getSpells(); //TODO: implémenter les attributs et méthode lié aux spells
        foreach ($spells as $spell) {
            if (isset($spell['maxUse'])) {
                $spell['remainingUse'] = $spell['maxUse'];
            }
        }
        $actor->setSpells($spells);

        $events[] = "{$actor->getName()} effectue un repos long, récupère $hpGain PV, toute sa stamina et tous ses sorts sont restaurés.";

        return [
            'events' => $events,
            'result' => [
                'actor' => $actor->toArray(),
                'target' => $target?->toArray(), //TODO: mettre a jour la fonction toArray fromArray pour que le passage de Json a un objet Character fonctionne correctement et inversement.
            ],
        ];
    }
}