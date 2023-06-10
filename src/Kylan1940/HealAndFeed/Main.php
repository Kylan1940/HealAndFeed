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
  
  const CONFIG_VERSION = 6;
  
  public function onEnable() : void {
        $this->updateConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
  }
  
  private function updateConfig(){
        if (!file_exists($this->getDataFolder() . 'config.yml')) {
            $this->saveResource('config.yml');
            return;
        }
        if ($this->getConfig()->get('config-version') !== self::CONFIG_VERSION) {
            $config_version = $this->getConfig()->get('config-version');
            $this->getLogger()->info("Your Config isn't the latest. We renamed your old config to §bconfig-" . $config_version . ".yml §6and created a new config");
            rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config-' . $config_version . '.yml');
            $this->saveResource('config.yml');
        }
  }
   
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if($sender instanceof Player){
                if($cmd->getName() == "heal"){
                  if ($sender -> hasPermission("healandfeed-heal.command")) {
                    if (isset($args[0])){
                       if ($sender -> hasPermission("healandfeed-healother.command")) {
                         $player = $this->getServer()->getPlayerExact($args[0]);
                         if ($player){
                           $player->setHealth($player->getMaxHealth());
                           $player->sendMessage($this->getConfig()->get("message-heal"));  
                         } else {
                           $sender->sendMessage($this->getConfig()->get("no-player-found"));
                         }
                       } else {
                        $sender->sendMessage($this->getConfig()->get("no-permission-healother"));
                       }
                   } else {
                       $sender->setHealth($sender->getMaxHealth());
                       $sender->sendMessage($this->getConfig()->get("message-heal"));  
                   }
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-heal"));
                  }
                }
                if($cmd->getName() == "healall"){
                  if ($sender -> hasPermission("healandfeed-healall.command")) {
                       if($this->getServer()->getOnlinePlayers() == null){
                         $sender->sendMessage($this->getConfig()->get("no-player-online"));
                       } else {
                         foreach($this->getServer()->getOnlinePlayers() as $online){
                            $online->setHealth($online->getMaxHealth());
                            $online->sendMessage($this->getConfig()->get("message-heal"));  
                         }
                       }
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-healall"));
                  }
                }
                if($cmd->getName() == "feed"){
                  if ($sender -> hasPermission("healandfeed-heal.command")) {
                    if (isset($args[0])){
                      if ($sender -> hasPermission("healandfeed-feedother.command")) {
                        $player = $this->getServer()->getPlayerExact($args[0]);
                        if ($player){
                           $player->getHungerManager()->setFood(20);
                           $player->getHungerManager()->setSaturation(20);
                           $player->sendMessage($this->getConfig()->get("message-heal"));  
                        } else {
                         $sender->sendMessage($this->getConfig()->get("no-player-found"));
                        } 
                      } else {
                        $sender->sendMessage($this->getConfig()->get("no-permission-feedother"));
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
                if($cmd->getName() == "feedall"){
                  if ($sender -> hasPermission("healandfeed-feedall.command")) {
                       if($this->getServer()->getOnlinePlayers() == null){
                         $sender->sendMessage($this->getConfig()->get("no-player-online"));
                       } else {
                         foreach($this->getServer()->getOnlinePlayers() as $online){
                            $online->getHungerManager()->setFood(20);
                            $online->getHungerManager()->setSaturation(20);
                            $online->sendMessage($this->getConfig()->get("message-feed"));  
                         }
                       }
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-feedall"));
                  }
                }
                if($cmd->getName() == "healfeed"){
                  $this->HealFeed($sender);
                } else {
                  $sender->sendMessage($this->getConfig()->get("no-permission-ui"));
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
                           $sender->sendMessage($this->getConfig()->get("no-player-found"));
                       }
                   } else {
                       $sender->sendMessage($this->getConfig()->get("console-command-heal"));  
                   }
                }
                if($cmd->getName() == "healall"){
                       if($this->getServer()->getOnlinePlayers() == null){
                         $sender->sendMessage($this->getConfig()->get("no-player-online"));
                       } else {
                         foreach($this->getServer()->getOnlinePlayers() as $online){
                            $online->setHealth($online->getMaxHealth());
                            $online->sendMessage($this->getConfig()->get("message-heal"));
                            $sender->sendMessage($this->getConfig()->get("message-healall"));
                         }
                       }
                }
                if($cmd->getName() == "feed"){
                  if (isset($args[0])){
                       $player = $this->getServer()->getPlayerExact($args[0]);
                       if ($player){
                           $player->getHungerManager()->setFood(20);
                           $player->getHungerManager()->setSaturation(20);
                           $player->sendMessage($this->getConfig()->get("message-feed"));  
                       } else {
                           $sender->sendMessage($this->getConfig()->get("no-player-found"));
                       }
                   } else {
                       $sender->sendMessage($this->getConfig()->get("console-command-feed"));  
                   }
                }
                if($cmd->getName() == "feedall"){
                       if($this->getServer()->getOnlinePlayers() == null){
                         $sender->sendMessage($this->getConfig()->get("no-player-online"));
                       } else {
                         foreach($this->getServer()->getOnlinePlayers() as $online){
                            $online->getHungerManager()->setFood(20);
                            $online->getHungerManager()->setSaturation(20);
                            $online->sendMessage($this->getConfig()->get("message-feed"));  
                            $sender->sendMessage($this->getConfig()->get("message-feedall"));
                         }
                       }
                }
                if($cmd->getName() == "healfeed"){
                  $sender->sendMessage($this->getConfig()->get("console-command-ui"));  
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
            $form->setTitle($this->getConfig()->get("title"));
            $form->addButton($this->getConfig()->get("button-heal"));
            $form->addButton($this->getConfig()->get("button-feed"));
            $form->sendToPlayer($sender);
            return $form;
    }
}
