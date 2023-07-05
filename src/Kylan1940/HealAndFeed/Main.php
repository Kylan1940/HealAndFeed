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
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};

class Main extends PluginBase implements Listener {
  
  const CONFIG_VERSION = 7;
  const PREFIX = "prefix";
  
  public function onEnable() : void {
    $this->updateConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
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
    $heal = $this->getConfig()->getNested('money.heal'); 
    $feed = $this->getConfig()->getNested('money.feed');
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
                  $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.heal')));
                }
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.all.heal'))); 
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
                $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.heal')));  
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.other.heal')));  
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
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.self.heal')));   
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
                  $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.feed')));
                }
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.all.feed'))); 
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
                $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.feed')));  
                $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.other.feed')));  
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
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.self.feed')));   
            } else {
              $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.feed.self')));
            } 
          }
        }
        if($cmd->getName() == "healfeed"){
          if($sender->hasPermission("healandfeed.ui")){
            $this->HFPerm($sender);
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.ui')));
          }  
        }
      }
      if($this->getConfig()->getNested('use') == "money"){
        
      }
    } 
    if(!$sender instanceof Player){
      if($cmd->getName() == "heal"){
        if (isset($args[0]) && $args[0] && $args[0] == "all"){
          if($this->getServer()->getOnlinePlayers() == null){
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.online')));
          } else {
            foreach($this->getServer()->getOnlinePlayers() as $online){
              $online->setHealth($online->getMaxHealth());
              $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.heal')));
            }
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.all.heal'))); 
          }
        }
        if (isset($args[0]) && $args[0] != "all"){
          $player = $this->getServer()->getPlayerExact($args[0]);
          if ($player){
            $player->setHealth($player->getMaxHealth());
            $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.heal')));  
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.other.heal')));  
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.found')));
          }
        }
        if (!isset($args[0])){
          $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('console-command.heal')));
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
              $online->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.feed')));
            }
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$playerGive, $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.all.feed'))); 
          }
        }
        if (isset($args[0]) && $args[0] != "all"){
          $player = $this->getServer()->getPlayerExact($args[0]);
          if ($player){
            $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
            $player->getHungerManager()->setSaturation(20);
            $player->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.other.feed')));  
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.sent.other.feed')));  
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-player.found')));
          }
        }
        if (!isset($args[0])){
          $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('console-command.feed')));
        }
      }
      if($cmd->getName() == "healfeed"){
        $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('console-command.ui')));  
      } 
    } 
    return true;
  }
   
  public function HFPerm($sender){
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
          if ($sender -> hasPermission("healandfeed.heal.self")) {
            $sender->setHealth($sender->getMaxHealth());
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.self.heal'))); 
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.heal.self')));
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
          if ($sender -> hasPermission("healandfeed.heal.self")) {
            $sender->getHungerManager()->setFood($sender->getHungerManager()->getMaxFood());
            $sender->getHungerManager()->setSaturation(20);
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('message.receive.self.feed'))); 
          } else {
            $sender->sendMessage(str_replace(["{playerGive}", "{playerName}", "{countOnline}"], [$args[0], $sender->getName(), count($this->getServer()->getOnlinePlayers())], $prefix.$this->getConfig()->getNested('no-permission.feed.self')));
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
