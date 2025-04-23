<?php
namespace App\Models\Actions;

require_once 'vendor/autoload.php';

use App\Models\Character;

class AbilityAction implements ActionInterface {
    public function execute(Character $character): string {
        return $character->getName() . " utilise une aptitude spéciale !";
    }
}