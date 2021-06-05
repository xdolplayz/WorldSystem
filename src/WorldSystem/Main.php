<?php 

namespace WorldSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use WorldSystem\commands\WorldSystemCommand;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
public static $config;

public function onEnable(): void{
@mkdir($this->getDataFolder() . "resources/");
self::$config = new Config($this->getDataFolder() . "resources/Config.yml", Config::YAML);
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getServer()->getCommandMap()->register("ws", new WorldSystemCommand($this));
$path = scandir($this->getServer()->getDataPath() . "worlds");
foreach($path as $world){
    if($world == "." || $world == "..") continue;
    if(!self::$config->__isset($world)){
        if($this->getServer()->getWorldManager()->isWorldLoaded($world)){
            self::$config->__set("$world", "true");
            self::$config->save();
        }else{
            self::$config->__set("$world", "false");
            self::$config->save();
        }
    }
    if(self::$config->__isset($world)){
        if(self::$config->__get($world) == "true"){
            $this->getServer()->getWorldManager()->loadWorld($world);
                }
            }
        }
    }
}


?>