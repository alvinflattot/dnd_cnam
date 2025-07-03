<?php

namespace App\Model;


use Exception;

//Prototype de la classe en attendant la vraie de l'équipe qui gère les personnages

class Character
{
    private string $name;
    private int $hp;
    private int $hpMax;
    private int $staminaMax;
    private int $stamina;
    private int $level;
    private int $armorClass;
    private array $inventory;
    private array $statistics;
    private array $skills;
    private array $spells = []; // prototype pour les sorts, à implémenter plus tard


    /**
     * @param string $name
     * @param int $hpMax
     * @param int $hp
     * @param int $staminaMax
     * @param int $stamina
     * @param int $armorClass
     * @param int $level
     * @param array $statistics
     * @param array $inventory
     * @param array $skills
     * @param array $spells
     */
    public function __construct(string $name, int $hpMax, int $hp, int $staminaMax, int $stamina, int $armorClass, int $level, array $inventory = [], array $spells = [], array $statistics = [], array $skills = [])
    {
        $this->setName($name);
        $this->setHpMax($hpMax);
        $this->setHp($hp);
        $this->setStaminaMax($staminaMax);
        $this->setStamina($stamina);
        $this->setArmorClass($armorClass);
        $this->setLevel($level);
        $this->setInventory($inventory);
        $this->setSpells($spells);
        $this->setStatistics($statistics);
        $this->setSkills($skills);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['hpMax'],
            $data['hp'],
            $data['staminaMax'],
            $data['stamina'],
            $data['armorClass'],
            $data['level'],
            $data['inventory'] ?? [],
            $data['spells'] ?? [],
            $data['statistics'],
            $data['skills'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'hpMax' => $this->getHpMax(),
            'hp' => $this->getHp(),
            'staminaMax' => $this->getStaminaMax(),
            'stamina' => $this->getStamina(),
            'armorClass' => $this->getArmorClass(),
            'level' => $this->getLevel(),
            'inventory' => $this->getInventory(),
            'spells' => $this->getSpells(),
            'statistics' => $this->getStatistics(),
            'skills' => $this->getSkills(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function setHp(int $hp): void
    {
        $this->hp = min($hp, $this->getHpMax());
    }

    public function getHpMax(): int
    {
        return $this->hpMax;
    }

    public function setHpMax(int $hpMax): void
    {
        $this->hpMax = $hpMax;
    }

    public function getStaminaMax(): int
    {
        return $this->staminaMax;
    }

    public function setStaminaMax(int $staminaMax): void
    {
        $this->staminaMax = $staminaMax;
    }

    public function getStamina(): int
    {
        return $this->stamina;
    }

    public function setStamina(int $stamina): void
    {
        $this->stamina = $stamina;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getArmorClass(): int
    {
        return $this->armorClass;
    }

    public function setArmorClass(int $armorClass): void
    {
        $this->armorClass = $armorClass;
    }

    public function getStatistics(): array
    {
        return $this->statistics;
    }

    public function setStatistics(array $statistics): void
    {
        $this->statistics = $statistics;
    }

    public function getInventory(): array
    {
        return $this->inventory;
    }

    public function setInventory(array $inventory): void
    {
        $this->inventory = $inventory;
    }

    public function getSkills(): array
    {
        return $this->skills;
    }

    public function setSkills(array $skills): void
    {
        $this->skills = $skills;
    }

    public function getSpells(): array
    {
        return $this->spells;
    }

    public function setSpells(array $spells): void
    {
        $this->spells = $spells;
    }

    /**
     *
     * @param string $itemToConsume
     * @throws Exception
     */
    public function consumeItem(string $itemToConsume): void
    {
        $inventory = $this->getInventory();

        if (!isset($inventory[$itemToConsume]) || $inventory[$itemToConsume] < 1) {
            throw new Exception("Le personnage ne possède pas l'objet « $itemToConsume ».");
        }

        $inventory[$itemToConsume]--;

        $this->setInventory($inventory);
    }

    function ajouterObjetInventaire(string $nomObjet, int $quantite = 1): void
    {
        $inventory = $this->getInventory();
        $inventory[$nomObjet] = ($inventory[$nomObjet] ?? 0) + $quantite;
        $this->setInventory($inventory);
    }

    /**
     * Calcule le modificateur de caractéristique
     * @throws Exception
     */
    public function getModifierBonus(string $statistic): int
    {
        if (!isset($this->statistics[$statistic])) {
            throw new Exception("Le personnage ne possède cette statistique « $statistic ».");
        }
        return (int)floor(($this->statistics[$statistic] - 10) / 2);
    }

    /**
     * Calcule le bonus de maîtrise.
     * Bonus de maîtrise (+2 à +6).
     */
    function getProficiencyBonus(): int
    {
        return (int)floor(($this->getLevel() - 1) / 4) + 2;
    }

    /**
     * Vérifie si un item est présent dans l'inventaire.
     */
    public function hasItem(string $itemName): bool
    {
        return isset($this->inventory[$itemName]) && $this->inventory[$itemName] > 0;
    }

}
