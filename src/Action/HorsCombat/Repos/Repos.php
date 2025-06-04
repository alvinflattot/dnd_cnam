<?php

namespace App\Action\HorsCombat;

use App\Action\ActionInterface;
use App\Model\Character;

interface ReposActionInterface extends ActionInterface
{
    public function execute(Character $actor, ?Character $target, array $params): array;
}