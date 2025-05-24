<?php
namespace App\Action\Chain;

use App\Model\Character;
use Exception;

class ValidationHandler extends AbstractActionHandler
{
    /**
     * @throws Exception
     */
    public function handle(string $actionType, Character $actor, ?Character $target, array $context): array
    {
        if (!isset($actionType)) {
            throw new Exception('Type d\'action manquant');
        }

        if ($actor->getHp() <= 0){
            throw new Exception('Le personnage est K.O.');
        }

        if ($actionType === 'attack' && !$target) {
            throw new Exception('Cible requise pour l\'attaque');
        }

        if ($actionType === 'useItem'){
            $itemToUse = $context['itemToUse'] ?? null;
            if (!is_string($itemToUse) || $itemToUse === '') {
                throw new Exception('Aucun objet à utiliser choisi.');
            }

            $inventory = $actor->getInventory();

            if (!isset($inventory[$itemToUse]) || $inventory[$itemToUse] <= 0) {
                throw new Exception(
                    "Le personnage ne possède pas l'objet « {$itemToUse} »."
                );
            }
        }

        return parent::handle($actionType, $actor, $target, $context);
    }
}