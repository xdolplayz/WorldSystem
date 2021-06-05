<?php 
namespace WorldSystem\commands;
use pocketmine\world\{ WorldManager as PMWorldManager, WorldCreationOptions };
use pocketmine\command\{ CommandSender, ConsoleCommandSender, Command };
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use WorldSystem\Main;
use pocketmine\math\Vector3;
use pocketmine\permission\DefaultPermissions;
use pocketmine\utils\Config;

class WorldSystemCommand extends Command{

public function __construct(){
    parent::__construct("ws", "Base World System command.");
}

public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
    if($sender->hasPermission("wm.cmd") || $sender->hasPermission(DefaultPermissions::ROOT_OPERATOR) || $sender instanceof ConsoleCommandSender){
    $help = "§9§l----§6World System Help§9§l----\n§r§a/ws info - Extra info about the plugin.\n/ws create (world name) - Creates a world.\n/ws load (world name) - Loads a specific world.\n/ws unload (world name) - Unloads a specified world.\n/ws tp (world name) - Teleports you to the specified world if it's loaded.\n/ws list - Sends a list of all worlds and whether they are loaded or not (loaded in green, unloaded in red).\n§l§9--------------------------";
    $info = "§9§l----§6World System Info§9§l----\n§r§a/ws create: The world create command doesn't have much customization like generators, seed, spawnpoint etc.\n I'm not expecting many people to use this plugin which is why it's not very customizable, if it gets support I'll add it later but for now this should be good.\n/ws load and /ws unload: When the server first turns on it gets all worlds and automatically puts them into a Config file (you can manually edit this but you dont need to, path is plugin_data/WorldSystem/resources/Config.yml), when you create a world it automatically sets to true, when you unload a world it sets this to false.  When it's true, the world will auto load when the server turns on.  When it's false, the world will not auto load when the server turns on.\n§l§9--------------------------";
    if(!isset($args[0])){
        $sender->sendMessage($help);
        return true;
    }else{
        switch($args[0]){
            case "help":
                $sender->sendMessage($help);
                return true;
            break;

            case "create":
                if(!isset($args[1])){
                    $sender->sendMessage("§cYou must enter a name for the world!");
                    return true;
                }
                if(!isset($args[2])){
                    $wco = WorldCreationOptions::create();
                    $sender->getServer()->getWorldManager()->generateWorld($args[1], $wco);
                    $sender->getServer()->getWorldManager()->loadWorld($args[1]);
                    $n = $args[1];
                    $sender->sendMessage("§aCreated a world called $n.");
                    Main::$config->__set("$n", "true");
                    Main::$config->save();
                    return true;
                }
            break;

            case "tp":
                case "teleport":
                    if(!isset($args[1])){ $sender->sendMessage("§cYou need to enter a world to teleport to!"); return true;}
                    $name = $args[1];
                    if(!$sender->getServer()->getWorldManager()->isWorldGenerated($name)){
                        $sender->sendMessage("§c$name isn't a world! (You can run /ws create (world name) to create a world and /ws list to see all worlds.)");
                        return true;
                    }
                    if(!$sender->getServer()->getWorldManager()->isWorldLoaded($name)){
                        $sender->sendMessage("§c$name isn't loaded! (You can use /ws load (world name) to load it.)");
                    }
                    $player = $sender->getServer()->getPlayerExact($sender->getName());
                    $world = $sender->getServer()->getWorldManager()->getWorldByName($name);
                    if($world == null){
                        $sender->sendMessage("§cFailed to teleport to world, try using /ws load (world name).");
                        return true;
                    }
                    $sender->sendMessage("§aTeleporting to $name...");
                    $player->teleport($world->getSafeSpawn());
                    $sender->sendMessage("§aTelported to $name!");
            break;

            case "load":
                if(!isset($args[1])){ $sender->sendMessage("§cYou need to enter a world!"); return true;}
                $name = $args[1];
                    if(!$sender->getServer()->getWorldManager()->isWorldGenerated($name)){
                        $sender->sendMessage("§c$name isn't a world! (You can run /ws create (world name) to create a world and /ws list to see all worlds.)");
                        return true;
                    }
                    if($sender->getServer()->getWorldManager()->isWorldLoaded($name)){
                        $sender->sendMessage("§c$name is already loaded!");
                        return true;
                    }
                    $sender->sendMessage("§aLoading $name...");
                    $sender->getServer()->getWorldManager()->loadWorld($args[1]);
                    Main::$config->__set("$name", "true");
                    Main::$config->save();
                    sleep(1);
                    $sender->sendMessage("§aLoaded $name!");
            break;

            case "unload":
                if(!isset($args[1])){ $sender->sendMessage("§cYou need to enter a world!"); return true;}
                $name = $args[1];
                    if(!$sender->getServer()->getWorldManager()->isWorldGenerated($name)){
                        $sender->sendMessage("§c$name isn't a world! (You can run /ws create (world name) to create a world and /ws list to see all worlds.)");
                        return true;
                    }
                    if(!$sender->getServer()->getWorldManager()->isWorldLoaded($name)){
                        $sender->sendMessage("§c$name is already unloaded!");
                        return true;
                    }
                    $world = $sender->getServer()->getWorldManager()->getWorldByName($name);
                    $sender->sendMessage("§aUnloading $name...");
                    $sender->getServer()->getWorldManager()->unLoadWorld($world);
                    Main::$config->set("$name", "false");
                    Main::$config->save();
                    sleep(1);
                    $sender->sendMessage("§aUnloaded $name!");
            break;

            case "list":
                $path = scandir($sender->getServer()->getDataPath() . "worlds");
                $sender->sendMessage("§9§l----§6World List§9----");
                foreach($path as $world){
                    if($world == "." || $world == "..") continue;
                        if(!$sender->getServer()->getWorldManager()->isWorldLoaded($world)){
                            $sender->sendMessage("§c$world");
                        }
                        if($sender->getServer()->getWorldManager()->isWorldLoaded($world)){
                            $sender->sendMessage("§a$world");
                        }
                    }
                    $sender->sendMessage("§9§l-------------------");
            break;

            case "info":
                $sender->sendMessage($info);
                return true;
            break;
        }
    }
}else{
    $sender->sendMessage("§cYou don't have permission to use this command!");
    return true;
    }
    return true;
    }
}


?>