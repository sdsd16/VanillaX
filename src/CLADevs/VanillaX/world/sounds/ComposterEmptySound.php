<?php

namespace CLADevs\VanillaX\world\sounds;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\world\sound\Sound;

class ComposterEmptySound implements Sound{

    public function encode(?Vector3 $pos): array{
        return [LevelSoundEventPacket::nonActorSound(LevelSoundEvent::BLOCK_COMPOSTER_EMPTY, $pos, false)];
    }
}