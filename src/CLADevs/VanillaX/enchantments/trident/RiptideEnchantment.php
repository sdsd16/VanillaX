<?php

namespace CLADevs\VanillaX\enchantments\trident;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\lang\KnownTranslationFactory;

class RiptideEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_tridentRiptide(), Rarity::RARE, ItemFlags::TRIDENT, ItemFlags::NONE, 3);
    }

    public function getId(): string{
        return "riptide";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::RIPTIDE;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::LOYALTY, EnchantmentIds::CHANNELING];
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::TRIDENT;
    }
}