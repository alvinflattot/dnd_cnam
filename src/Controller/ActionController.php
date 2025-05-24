<?php

namespace App\Controller;

use App\Action\Chain\EventDispatchHandler;
use App\Action\Chain\ExecutionHandler;
use App\Action\Chain\ValidationHandler;
use App\Model\Character;
use App\Util\ResponseUtil;
use Exception;

class ActionController
{
    /**
     * @throws Exception
     */
    public static function execute(array $data): void
    {
        $actor = Character::fromArray($data['actor']);
        $target = isset($data['target']) ? Character::fromArray($data['target']) : null;

        $chain = new ValidationHandler();
        $chain->setNext(new ExecutionHandler())
            ->setNext(new EventDispatchHandler());

        $output = $chain->handle(
            $data['actionType'],
            $actor,
            $target,
            $data['params']
        );

        ResponseUtil::json($output);
    }
}