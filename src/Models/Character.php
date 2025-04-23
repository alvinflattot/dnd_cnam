<?php
namespace App\Models;
use App\Models\Actions\ActionInterface;

require_once 'vendor/autoload.php';

class Character {
    private string $name;
    private int $spellSlots = 0; // slot par jour
    private ActionInterface $action;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function setAction(ActionInterface $action): void {
        $this->action = $action;
    }

    public function performAction(): string {
        return $this->action->execute($this);
    }

    public function getName(): string {
        return $this->name;
    }
}
