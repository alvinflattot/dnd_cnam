<?php

namespace App\Factory;

use App\Action\ActionInterface;
use App\Action\Combat\AttackAction;
use App\Action\HorsCombat\MoveAction;
use App\Action\HorsCombat\ReposCourt;
use App\Action\HorsCombat\ReposLong;
use App\Action\HorsCombat\SearchTreasure;
use App\Action\HorsCombat\UseItemAction;
use InvalidArgumentException;

class ActionFactory
{
    public static function create(string $type): ActionInterface
    {
        return match ($type) {
            'move' => new MoveAction(),
            'attack' => new AttackAction(),
            'useItem' => new UseItemAction(),
            'SearchTreasure' => new SearchTreasure(),
            'reposCourt' => new ReposCourt(),
            'reposLong' => new ReposLong(),
            default => throw new InvalidArgumentException("Action inconnue \"$type\"")
        };
    }
}
