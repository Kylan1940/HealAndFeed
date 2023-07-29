<?php

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI; 
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use Kylan1940\HealAndFeed\form\{Form, SimpleForm};
use Kylan1940\HealAndFeed\UpdateNotifier\{ConfigUpdater};

class Main extends PluginBase implements Listener {
  
  const PREFIX = "prefix";
  
  public function onEnable() : void {
    ConfigUpdater::update($this);
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getResource("config.yml");
  }
   
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
    $healprice = $this->getConfig()->getNested('money.heal'); 
    $feedprice = $this->getConfig()->getNested('money.feed');
    $prefix = $this->getConfig()->getNested(self::PREFIX);
    // playerGive variable
    $playerGive = "";
    foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
      $onlinePlayers = $onlinePlayer->getName();
      $playerGive .= $onlinePlayers . ", ";
    }
    $playerGive = rtrim($playerGive, ", ");
    if($sender instanceof Player){
      if($this->getConfig()->getNested('use') == "permission"){
        if($cmd->getName() == "heal"){
          if (isset($args[0]) && $args[0] == "all"){
            if ($sender -> hasPermission("healandfeed.heal.all")) {
              if($this->getServer()->getOnlinePlayers() == null){
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.online')));
              } else {
                foreach($this->getServer()->getOnlinePlayers() as $online){
                  $online->setHealth($online->getMaxHealth());
                  $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.heal.other')));
                }
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.heal.all'))); 
              }
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.heal.all')));
            }
          }
          if (isset($args[0]) && $args[0] != "all"){
            if ($sender -> hasPermission("healandfeed.heal.other")) {
              $player = $this->getServer()->getPlayerExact($args[0]);
              if ($player){
                $player->setHealth($player->getMaxHealth());
                $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.heal.other')));  
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.heal.other')));  
              } else {
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.found')));
              }
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.heal.other')));
            }
          }
          if (!isset($args[0])){
            if ($sender -> hasPermission("healandfeed.heal.self")) {
              $sender->setHealth($sender->getMaxHealth());
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.heal.self')));   
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.heal.self')));
            } 
          }
        }
        if($cmd->getName() == "feed"){
          if (isset($args[0]) && $args[0] == "all"){
            if ($sender -> hasPermission("healandfeed.feed.all")) {
              if($this->getServer()->getOnlinePlayers() == null){
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.online')));
              } else {
                foreach($this->getServer()->getOnlinePlayers() as $online){
                  $online->getHungerManager()->setFood($online->getHungerManager()->getMaxFood());
                  $online->getHungerManager()->setSaturation(20);
                  $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.feed.other')));
                }
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.feed.all'))); 
              }
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.feed.all')));
            }
          }
          if (isset($args[0]) && $args[0] != "all"){
            if ($sender -> hasPermission("healandfeed.feed.other")) {
              $player = $this->getServer()->getPlayerExact($args[0]);
              if ($player){
                $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                $player->getHungerManager()->setSaturation(20);
                $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.feed.other')));  
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.feed.other')));  
              } else {
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.found')));
              }
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.feed.other')));
            }
          }
          if (!isset($args[0])){
            if ($sender -> hasPermission("healandfeed.feed.self")) {
              $sender->getHungerManager()->setFood($sender->getHungerManager()->getMaxFood());
              $sender->getHungerManager()->setSaturation(20);
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.feed.self')));   
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.feed.self')));
            } 
          }
        }
        if($cmd->getName() == "healfeed"){
          if($sender->hasPermission("healandfeed.ui")){
            $this->HealFeed($sender);
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.ui')));
          }  
        }
      }
      if($this->getConfig()->getNested('use') == "money"){
        if($cmd->getName() == "heal"){
          if(isset($args[0]) && $args[0] == "all"){
            BedrockEconomy::reduceMoney($sender, $healprice, static function(bool $success) use ($sender, $price): void {
              
            });
          }
        }
      }
      if($this->getConfig()->getNested('use') == "both"){
        
      }
      if($this->getConfig()->getNested('use') != "permission" && $this->getConfig()->getNested('use') != "money" && $this->getConfig()->getNested('use') != "both"){
        $sender->sendMessage($prefix."Your 'use' configuration is wrong, check the instructions, if there's problem again, please report the plugin!");
      }
    } 
    if(!$sender instanceof Player){
      if($cmd->getName() == "heal"){
        if (isset($args[0]) && $args[0] == "all"){
          if($this->getServer()->getOnlinePlayers() == null){
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.online')));
          } else {
            foreach($this->getServer()->getOnlinePlayers() as $online){
              $online->setHealth($online->getMaxHealth());
              $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.heal.other')));
            }
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.heal.all'))); 
          }
        }
        if (isset($args[0]) && $args[0] != "all"){
          $player = $this->getServer()->getPlayerExact($args[0]);
          if ($player){
            $player->setHealth($player->getMaxHealth());
            $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.heal.other')));  
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.heal.other')));  
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.found')));
          }
        }
        if (!isset($args[0])){
          $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], ["NONE", $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('console-command.heal')));
        }
      }
      if($cmd->getName() == "feed"){
        if (isset($args[0]) && $args[0] && $args[0] == "all"){
          if($this->getServer()->getOnlinePlayers() == null){
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.online')));
          } else {
            foreach($this->getServer()->getOnlinePlayers() as $online){
              $online->getHungerManager()->setFood($online->getHungerManager()->getMaxFood());
              $online->getHungerManager()->setSaturation(20);
              $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.feed.other')));
            }
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.feed.all'))); 
          }
        }
        if (isset($args[0]) && $args[0] != "all"){
          $player = $this->getServer()->getPlayerExact($args[0]);
          if ($player){
            $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
            $player->getHungerManager()->setSaturation(20);
            $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.feed')));  
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.feed.other')));  
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.found')));
          }
        }
        if (!isset($args[0])){
          $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], ["NONE", $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('console-command.feed')));
        }
      }
      if($cmd->getName() == "healfeed"){
        $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], ["NONE", $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('console-command.ui')));  
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
          $prefix = $this->getConfig()->getNested(self::PREFIX);
          // playerGive variable
          $playerGive = "";
          foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
            $onlinePlayers = $onlinePlayer->getName();
            $playerGive .= $onlinePlayers . ", ";
          }
          $playerGive = rtrim($playerGive, ", ");
          
          if($this->getConfig()->getNested('use') == "permission"){
            if($sender->hasPermission("healandfeed.heal.self")){
              $sender->setHealth($sender->getMaxHealth());
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.heal.self')));   
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.heal.self')));
            }
          }
          if($this->getConfig()->getNested('use') == "money"){
            
          }
          if($this->getConfig()->getNested('use') == "both"){
            
          }
          if($this->getConfig()->getNested('use') != "permission" && $this->getConfig()->getNested('use') != "money" && $this->getConfig()->getNested('use') != "both"){
            $sender->sendMessage($prefix."Your 'use' configuration is wrong, check the instructions, if there's problem again, please report the plugin!");
          }
          break;
        case 1:
          $prefix = $this->getConfig()->getNested(self::PREFIX);
          // playerGive variable
          $playerGive = "";
          foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
            $onlinePlayers = $onlinePlayer->getName();
            $playerGive .= $onlinePlayers . ", ";
          }
          $playerGive = rtrim($playerGive, ", ");
          
          if($this->getConfig()->getNested('use') == "permission"){
            if($sender->hasPermission("healandfeed.feed.self")){
              $sender->getHungerManager()->setFood($sender->getHungerManager()->getMaxFood());
              $sender->getHungerManager()->setSaturation(20);
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.feed.self')));   
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.feed.self')));
            }
          }
          if($this->getConfig()->getNested('use') == "money"){
            
          }
          if($this->getConfig()->getNested('use') == "both"){
            
          }
          if($this->getConfig()->getNested('use') != "permission" && $this->getConfig()->getNested('use') != "money" && $this->getConfig()->getNested('use') != "both"){
            $sender->sendMessage($prefix."Your 'use' configuration is wrong, check the instructions, if there's problem again, please report the plugin!");
          }
          break;
      }
    });
    $form->setTitle($this->getConfig()->getNested('ui.title'));
    $form->addButton($this->getConfig()->getNested('ui.button.heal'),1,$this->getConfig()->getNested('ui.button.image.heal'));
    $form->addButton($this->getConfig()->getNested('ui.button.feed'),1,$this->getConfig()->getNested('ui.button.image.feed'));
    $form->sendToPlayer($sender);
    return $form;
    }
}
