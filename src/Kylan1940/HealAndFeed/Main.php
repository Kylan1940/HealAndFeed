<?php

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
  
  public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
        
        // Check config
        if($this->getConfig()->get("config-ver") != 3)
        {
            $this->getLogger()->info("HealAndFeed's config is NOT up to date. Please delete the config.yml and restart the server or the plugin may not work properly.");
        }
  }
   
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if($sender instanceof Player){
                if($cmd->getName() == "heal"){
                  if ($sender -> hasPermission("healandfeed-heal.command")) {
                    $sender->setHealth($sender->getMaxHealth());
                    $sender->sendMessage($this->getConfig()->get("message-heal")); 
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-heal"));
                  }
                }
                if($cmd->getName() == "feed"){
                  if ($sender -> hasPermission("healandfeed-feed.command")) {
                    $sender->getHungerManager()->setFood(20);
                    $sender->getHungerManager()->setSaturation(20);
                    $sender->sendMessage($this->getConfig()->get("message-feed")); 
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-feed"));
                  }
                } 
        } else {
          $sender->sendMessage($this->getConfig()->get("only-ingame"));
        }
        return true;
    }

}
