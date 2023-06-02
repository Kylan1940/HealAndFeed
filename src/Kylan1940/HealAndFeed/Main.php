<?php

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};

class Main extends PluginBase implements Listener {
  
  public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
  }
   
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if($sender instanceof Player){
                if($cmd->getName() == "heal"){
                  if ($sender -> hasPermission("healandfeed-heal.command")) {
                    if (isset($args[0])){
                       $player = $this->getServer()->getPlayerExact($args[0]);
                       if ($player){
                           $player->setHealth($sender->getMaxHealth());
                           $player->sendMessage($this->getConfig()->get("message-heal"));  
                       } else {
                           //$sender->sendMessage($this->getConfig()->get("no-player-found"));
                           $sender->sendMessage("§cThis player does not exist");
                       }
                   } else {
                       $sender->setHealth($player->getMaxHealth());
                       $sender->sendMessage($this->getConfig()->get("message-heal"));  
                   }
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-heal"));
                  }
                }
                if($cmd->getName() == "feed"){
                  if ($sender -> hasPermission("healandfeed-heal.command")) {
                    if (isset($args[0])){
                       $player = $this->getServer()->getPlayerExact($args[0]);
                       if ($player){
                           $player->getHungerManager()->setFood(20);
                           $player->getHungerManager()->setSaturation(20);
                           $player->sendMessage($this->getConfig()->get("message-heal"));  
                       } else {
                           //$sender->sendMessage($this->getConfig()->get("no-player-found"));
                           $sender->sendMessage("§cThis player does not exist");
                       }
                   } else {
                       $sender->getHungerManager()->setFood(20);
                       $sender->getHungerManager()->setSaturation(20);
                       $sender->sendMessage($this->getConfig()->get("message-heal"));  
                   }
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-heal"));
                  }
                } 
                if($cmd->getName() == "healfeed"){
                  $this->HealFeed($sender);
                }
        } 
        if(!$sender instanceof Player){
                if($cmd->getName() == "heal"){
                    if (isset($args[0])){
                       $player = $this->getServer()->getPlayerExact($args[0]);
                       if ($player){
                           $player->setHealth($player->getMaxHealth());
                           $player->sendMessage($this->getConfig()->get("message-heal"));  
                       } else {
                           //$sender->sendMessage($this->getConfig()->get("no-player-found"));
                           $sender->sendMessage("§cThis player does not exist");
                       }
                   } else {
                       //$sender->sendMessage($this->getConfig()->get("console-command-heal"));  
                       $sender->sendMessage("§c/heal is heal for playerself and only in-game, for console you must /heal (playerOnline)"); 
                   }
                }
                if($cmd->getName() == "feed"){
                  if (isset($args[0])){
                       $player = $this->getServer()->getPlayerExact($args[0]);
                       if ($player){
                           $player->getHungerManager()->setFood(20);
                           $player->getHungerManager()->setSaturation(20);
                           $player->sendMessage($this->getConfig()->get("message-heal"));  
                       } else {
                           //$sender->sendMessage($this->getConfig()->get("no-player-found"));
                           $sender->sendMessage("§cThis player does not exist");
                       }
                   } else {
                       //$sender->sendMessage($this->getConfig()->get("console-command-feed"));  
                       $sender->sendMessage("§c/feed is heal for playerself and only in-game, for console you must /feed (playerOnline)");
                   }
                }
                if($cmd->getName() == "healfeed"){
                  //$sender->sendMessage($this->getConfig()->get("console-command-ui"));  
                       $sender->sendMessage("§c/healfeed is only for in-game");
                } 
        } 
    return true;
  }
   
  public function HealFeed($sender){
        $form = new SimpleForm(function (Player $sender, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    if ($sender -> hasPermission("healandfeed-heal.command")) {
                      $sender->setHealth($sender->getMaxHealth());
                      $sender->sendMessage($this->getConfig()->get("message-heal")); 
                    } else {
                      $sender->sendMessage($this->getConfig()->get("no-permission-heal"));
                    }
                  break;
                case 1:
                    if ($sender -> hasPermission("healandfeed-feed.command")) {
                      $sender->getHungerManager()->setFood(20);
                      $sender->getHungerManager()->setSaturation(20);
                      $sender->sendMessage($this->getConfig()->get("message-feed")); 
                    } else {
                      $sender->sendMessage($this->getConfig()->get("no-permission-feed"));
                    }
                  break;
            }
        });
            //$form->setTitle($this->getConfig()->get("title"));
            $form->setTitle("§aHeal§7And§cFeed");
            //$form->addButton($this->getConfig()->get("button-heal"));
            $form->addButton("§aHeal");
            //$form->addButton($this->getConfig()->get("button-feed"));
            $form->addButton("§cFeed");
            $form->sendToPlayer($sender);
            return $form;
    }

}
