<?php

namespace App\Model;


use Exception;

class Character
{
    private string $name;
    private int $spellSlots;
    private int $hp;
    private int $armorClass;
    private array $inventory;
    private array $skills;
    private array $status;

    /**
     * @param string $name
     * @param int $hp
     * @param int $armorClass
     * @param array $inventory
     * @param array $skills
     * @param array $status
     * @param int $spellSlots
     */
    public function __construct(string $name, int $hp, int $armorClass, array $inventory = [], array $skills = [], array $status = [], int $spellSlots = 0)
    {
        $this->name = $name;
        $this->hp = $hp;
        $this->armorClass = $armorClass;
        $this->inventory = $inventory;
        $this->skills = $skills;
        $this->status = $status;
        $this->spellSlots = $spellSlots;
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
        $this->hp = $hp;
    }

    public function getArmorClass(): int
    {
        return $this->armorClass;
    }

    public function setArmorClass(int $armorClass): void
    {
        $this->armorClass = $armorClass;
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
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new Exception(
                'Donnée manquante ou invalide : name (string) requis'
            );
        }

        if (!isset($data['hp']) || !is_int($data['hp'])) {
            throw new Exception('Donnée manquante ou invalide : hp (int) requis');
        }

        if (!isset($data['armorClass']) || !is_int($data['armorClass'])) {
            throw new Exception('Donnée manquante ou invalide : armorClass (int) requis');
        }

        return new self(
            $data['name'],
            $data['hp'],
            $data['armorClass'],
            $data['inventory'] ?? [],
            $data['skills'] ?? [],
            $data['status'] ?? [],
            $data['spellSlot'] ?? 0
        );
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
            throw new Exception("Le personnage ne possède pas l'objet « {$itemToConsume} ».");
        }

        $inventory[$itemToConsume]--;

        $this->setInventory($inventory);
    }
}
