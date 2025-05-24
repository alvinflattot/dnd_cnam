<?php
namespace App\Action;

use App\Model\Character;

interface ActionInterface
{
    /**
     * @param Character      $actor
     * @param Character|null $target
     * @param array          $params
     * @return array
     */
    public function execute(Character $actor, ?Character $target, array $params): array;
}
