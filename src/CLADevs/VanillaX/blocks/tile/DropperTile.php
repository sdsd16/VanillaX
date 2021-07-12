<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\inventories\types\DropperInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Container;
use pocketmine\block\tile\ContainerTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;

class DropperTile extends Spawnable implements Container{
    use ContainerTrait;

    const TILE_ID = TileVanilla::DROPPER;
    const TILE_BLOCK = BlockLegacyIds::DROPPER;

    private DropperInventory $inventory;

    public function getInventory(): DropperInventory{
        return $this->inventory;
    }

    public function getRealInventory(): DropperInventory{
        return $this->inventory;
    }

    public function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new DropperInventory($this->getPos());
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}