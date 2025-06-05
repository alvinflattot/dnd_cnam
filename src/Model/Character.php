<?php

namespace App\Model;


use Exception;

class Character
{
    private string $name;
    private int $spellSlots;
    private int $hp;
    private int $hpMax;
    private int $level;
    private int $armorClass;
    private array $inventory;
    private array $statistics;
    private array $skills;
    private array $status;

    /**
     * @param string $name
     * @param int $hp
     * @param int $hpMax
     * @param int $armorClass
     * @param int $level
     * @param array $statistics
     * @param array $inventory
     * @param array $skills
     * @param array $status
     * @param int $spellSlots
     */
    public function __construct(string $name, int $hpMax, int $hp, int $armorClass, int $level, array $statistics = [], array $inventory = [], array $skills = [], array $status = [], int $spellSlots = 0)
    {
        $this->setName($name);
        $this->setHpMax($hpMax);
        $this->setHp($hp);
        $this->setArmorClass($armorClass);
        $this->setLevel($level);
        $this->setStatistics($statistics);
        $this->setInventory($inventory);
        $this->setSkills($skills);
        $this->setStatus($status);
        $this->setSpellSlots($spellSlots);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['hpMax'],
            $data['hp'],
            $data['armorClass'],
            $data['level'],
            $data['statistics'],
            $data['inventory'] ?? [],
            $data['skills'] ?? [],
            $data['status'] ?? [],
            $data['spellSlot'] ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'hpMax' => $this->getHpMax(),
            'hp' => $this->getHp(),
            'armorClass' => $this->getArmorClass(),
            'level' => $this->getLevel(),
            'statistics' => $this->getStatistics(),
            'spellSlots' => $this->getSpellSlots(),
            'inventory' => $this->getInventory(),
            'skills' => $this->getSkills(),
            'status' => $this->getStatus(),
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

    public function getSpellSlots(): int
    {
        return $this->spellSlots;
    }

    public function setSpellSlots(int $spellSlots): void
    {
        $this->spellSlots = $spellSlots;
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

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setStatus(array $status): void
    {
        $this->status = $status;
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
