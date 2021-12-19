<?php

declare(strict_types=1);

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener{
	
	public function onEnable() : void {
        $this->saveDefaultConfig();
        $this->reloadConfig();
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
      }
	
  public function onCommand(CommandSender $sender,Command $cmd,string $label,array $args) : bool{
           if ($sender instanceof Player) {
               if ($cmd->getName() == "heal") {
                       $sender->setHealth($sender->getMaxHealth());
                       $sender->sendMessage($this->getConfig()->get("message-heal")); 
               }
              if ($cmd->getName() == "feed") {
                       $sender->getHungerManager()->setFood(20);
                       $sender->getHungerManager()->setSaturation(20);
                       $sender->sendMessage($this->getConfig()->get("message-feed"));
              }
           } else {
               $sender->sendMessage("§cYou must be in-game to use this command!");
           }
          return true;
		}
}