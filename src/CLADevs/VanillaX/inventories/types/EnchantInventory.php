<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;

class EnchantInventory extends FakeBlockInventory{

    public function __construct(Position $holder){
        parent::__construct($holder, 2, BlockLegacyIds::AIR, WindowTypes::ENCHANTMENT);
    }

    public function handlePacket(Player $player, ServerboundPacket $packet): bool{
        if($packet instanceof ActorEventPacket && $packet->eventId === ActorEvent::PLAYER_ADD_XP_LEVELS){
            if(!$player->isCreative()){
                $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - abs($packet->eventData));
            }
        }
        return true;
    }


    /**
     * @param Player $player, returns player who successfully enchanted their item
     * @param Item $item, returns a new item after its enchanted
     */
    public function onSuccess(Player $player, Item $item): void{
    //Server::getInstance()->getLogger()->info("[".date("Y/m/d:H:i:s")."] > {$player->getName()}進行了附魔");
    }
}
