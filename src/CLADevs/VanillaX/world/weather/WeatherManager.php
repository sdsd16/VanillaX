<?php

namespace CLADevs\VanillaX\world\weather;

use CLADevs\VanillaX\configuration\Setting;
use CLADevs\VanillaX\entities\object\LightningBoltEntity;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use CLADevs\VanillaX\VanillaX;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class WeatherManager{
    use SingletonTrait;

    /** @var Weather[] */
    private array $weathers = [];

    public function __construct(){
        self::setInstance($this);
    }

    public function startup(): void{
        if(!Setting::getInstance()->isWeatherEnabled()){
            return;
        }
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            if(!GameRuleManager::getInstance()->getValue(GameRule::DO_WEATHER_CYCLE, $world)){
                continue;
            }
            $this->addWeather($world);
        }
        VanillaX::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void{
            foreach($this->weathers as $weather){
                if($weather->isRaining()){
                    $weather->duration--;

                    if($weather->duration < 1){
                        $weather->stopStorm();
                    }elseif($weather->isThundering() && mt_rand(1, 100000) === 0){
                        $players = Server::getInstance()->getOnlinePlayers();

                        if(count($players) >= 1){
                            $random = $players[array_rand($players)];
                            $location = $random->getLocation();
                            $location->x += mt_rand(0, 15);
                            $location->y += mt_rand(0, 15);
                            $entity = new LightningBoltEntity($location);
                            $entity->spawnToAll();
                        }
                    }
                }else{
                    $weather->delayDuration--;

                    if($weather->delayDuration < 1){
                        $weather->startStorm();
                    }
                }
                $weather->saveData();
            }
        }), 20);
    }

    public function addWeather(World $world): void{
        $this->weathers[strtolower($world->getFolderName())] = new Weather($world);
    }

    public function removeWeather(World $world): void{
        if(isset($this->weathers[strtolower($world->getFolderName())])){
            unset($this->weathers[strtolower($world->getFolderName())]);
        }
    }

    public function getWeather(World|string $world): ?Weather{
        $worldName = $world;

        if($world instanceof World){
            $worldName = $world->getFolderName();

            if(!isset($this->weathers[strtolower($worldName)])){
                $this->addWeather($world);
            }
        }
        return $this->weathers[strtolower($worldName)] ?? null;
    }

    public function isRaining(World $world, bool $checkThunder = true): bool{
        $weather = $this->weathers[strtolower($world->getFolderName())] ?? null;

        if($weather !== null){
            return $weather->isRaining() || (($checkThunder && $weather->isThundering()));
        }
        return false;
    }

    public function isThundering(World $world): bool{
        $weather = $this->weathers[strtolower($world->getFolderName())] ?? null;

        if($weather !== null && $weather->isThundering()){
            return true;
        }
        return false;
    }

    /**
     * @param Player|Player[]|null $player
     * @param bool $thunder
     */
    public function sendClear(null|Player|array $player = null, bool $thunder = false): void{
        if($player === null){
            $player = Server::getInstance()->getOnlinePlayers();
        }elseif($player instanceof Player){
            $player = [$player];
        }
        foreach($player as $p){
            $pk = new LevelEventPacket();
            $pk->eventId = LevelEvent::STOP_RAIN;
            $pk->eventData = 0;
            $pk->position = new Vector3(0, 0, 0);
            $p->getNetworkSession()->sendDataPacket($pk);

            if($thunder){
                $pk = new LevelEventPacket();
                $pk->eventData = LevelEvent::STOP_THUNDER;
                $pk->eventId = 0;
                $pk->position = new Vector3(0, 0, 0);
                $p->getNetworkSession()->sendDataPacket($pk);
            }
        }
    }

    /**
     * @param Player|Player[]|null $player
     * @param bool $thunder
     */
    public function sendWeather(Player|array $player = null, bool $thunder = false): void{
        if($player === null){
            $player = Server::getInstance()->getOnlinePlayers();
        }elseif($player instanceof Player){
            $player = [$player];
        }
        foreach($player as $p){
            $pk = new LevelEventPacket();
            $pk->eventId = LevelEvent::START_RAIN;
            $pk->eventData = 65535;
            $pk->position = new Vector3(0, 0, 0);
            $p->getNetworkSession()->sendDataPacket($pk);

            if($thunder){
                $pk = new LevelEventPacket();
                $pk->eventId = LevelEvent::START_THUNDER;
                $pk->eventData = 65535;
                $pk->position = new Vector3(0, 0, 0);
                $p->getNetworkSession()->sendDataPacket($pk);
            }
        }
    }
}