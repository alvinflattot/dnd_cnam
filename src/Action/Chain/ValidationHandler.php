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

        if ($actor->getHp() <= 0) {
            throw new Exception('Le personnage est K.O.');
        }

        if ($actionType === 'attack') {
            if (!$target) {
                throw new Exception('Cible requise pour l\'attaque');
            }
            if ($target->getHp() < 0) {
                throw new Exception("«{$target->getName()}» est déjà KO");
            }
            if (!isset($context['attackRoll']) || !is_numeric($context['attackRoll']) || $context['attackRoll'] <= 0 || $context['attackRoll'] > 100) {
                throw new Exception("Une valeur de dé d'attaque est invalide");
            }
            if (!isset($context['damageRoll']) || !is_numeric($context['damageRoll']) || $context['damageRoll'] <= 0 || $context['damageRoll'] > 100) {
                throw new Exception("Une valeur de dé de dommage est invalide");
            }

            // Utilisation d'une arme
            if(isset($context['weaponToUse'])){
                $weaponToUse = $context['weaponToUse'];
                if (!isset($weaponToUse['name']) || !isset($weaponToUse['statistic']) || !isset($weaponToUse['damage'])){
                    throw new Exception("Objet arme mal formé.");
                }

                if (!$actor->hasItem($weaponToUse['name'])){
                    throw new Exception("«{$actor->getName()}» le possède pas l'objet «{$weaponToUse['name']}»");
                }
            }
        }

        if ($actionType === 'useItem') {
            $itemToUse = $context['itemToUse'] ?? null;
            if (!is_string($itemToUse) || $itemToUse === '') {
                throw new Exception('Aucun objet à utiliser choisi.');
            }

            if (!$actor->hasItem($itemToUse)) {
                throw new Exception("Le personnage ne possède pas l'objet « $itemToUse ».");
            }
        }

        // Utilisation d'une fouille
        if($actionType === 'SearchTreasure') {
            $location = $context['location'] ?? null;
            if (!is_string($location) || $location === '') {
                throw new Exception('Localisation non trouvé.');
            }

        }

        return parent::handle($actionType, $actor, $target, $context);
    }
}