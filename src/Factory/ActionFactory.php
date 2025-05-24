<?php
namespace App\Factory;

use App\Action\ActionInterface;
use App\Action\HorsCombat\MoveAction;
use App\Action\Combat\AttackAction;
use App\Action\HorsCombat\UseItemAction;
use InvalidArgumentException;

class ActionFactory
{
    public static function create(string $type): ActionInterface
    {
        return match ($type) {
            'move'   => new MoveAction(),
            'attack' => new AttackAction(),
            'useItem' => new UseItemAction(),
            default  => throw new InvalidArgumentException("Action inconnue \"$type\"")
        };
    }
}
