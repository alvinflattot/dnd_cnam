<?php

namespace App\Controllers;
require_once 'vendor/autoload.php';

use App\Models\Actions\AbilityAction;
use App\Models\Character;
use App\Models\Actions\SpellAction;
use App\Models\Actions\AttackAction;
use Exception;

//require_once 'src/models/actions/SpellAction.php';


class CharacterController
{
    /**
     * @throws Exception
     */
    public function handleAction(string $actionType): string
    {
        $character = new Character("Aragorn");

        //TODO : vérifier si le personnage peux obtenir/faire l'action
        switch ($actionType) {
            case 'spell':
                $character->setAction(new SpellAction());
                break;
            case 'attack':
                $character->setAction(new AttackAction());
                break;
            case 'ability':
                $character->setAction(new AbilityAction());
                break;
            case 'jet de sauvegarde':
                $character->setAction(new AbilityAction());
                break;
            case 'getSpell':
                $character->setAction(new AbilityAction());
                break;
            case 'ability':
                $character->setAction(new AbilityAction());
                break;
            default:
                throw new Exception("Action inconnue");
        }

        return $character->performAction();
    }
}
