<?php

namespace CLADevs\VanillaX\blocks\types\structure;

use pocketmine\block\Solid;

class StructureBlock extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(self::STRUCTURE_BLOCK, $meta, "Structure Block");
    }
}