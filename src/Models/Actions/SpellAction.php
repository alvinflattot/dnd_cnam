<?php

namespace App\Models\Actions;

use App\Models\Character;

require_once 'vendor/autoload.php';

class SpellAction implements ActionInterface
{
    public function execute(Character $character): string
    {
        // Conditions et effets (ex: vérifier mana)
        return $character->getName() . " lance un sort puissant !";
    }
}
