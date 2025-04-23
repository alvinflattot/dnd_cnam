<?php
namespace App\Models\Actions;

use App\Models\Character;

require_once 'vendor/autoload.php';



class AttackAction implements ActionInterface {
    public function execute(Character $character): string {
        return $character->getName() . " attaque avec son arme !";
    }
}