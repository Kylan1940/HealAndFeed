<?php

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use onebone\economyapi\EconomyAPI;
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};

class Main extends PluginBase implements Listener {

   public function onEnable() : void {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
   }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if($sender instanceof Player){
          $economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
          $moneyheal = ($this->getConfig()->get("money-heal"));
          $moneyfeed = ($this->getConfig()->get("money-feed"));
          $money = $economy->myMoney($sender);
                if($cmd->getName() == "heal"){
                  if($money >= $moneyheal){
                    $economy->reduceMoney($sender, $moneyheal);
                    $sender->setHealth($sender->getMaxHealth());
                    $sender->sendMessage($this->getConfig()->get("message-heal"));
                  } else {
                    $sender->sendMessage($this->getConfig()->get("not-enough-money-heal"));
                  }
                }
                if($cmd->getName() == "feed"){
                  if($money >= $moneyfeed){
                    $economy->reduceMoney($sender, $moneyfeed);
                    $sender->getHungerManager()->setFood(20);
                    $sender->getHungerManager()->setSaturation(20);
                    $sender->sendMessage($this->getConfig()->get("message-feed"));
                  } else {
                    $sender->sendMessage($this->getConfig()->get("not-enough-money-feed"));
                  }
                } 
              if($cmd->getName() == "healfeed"){
                $this->HealFeed($sender);
              } 
        } else {
          $sender->sendMessage($this->getConfig()->get("only-ingame"));
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
                  $economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
                  $money = $economy->myMoney($sender); 
                  $moneyheal = ($this->getConfig()->get("money-heal"));
                  if($money >= $moneyheal){
                    $economy->reduceMoney($sender, $moneyheal);
                    $sender->setHealth($sender->getMaxHealth());
                    $sender->sendMessage($this->getConfig()->get("message-heal"));
                  } else {
                    $sender->sendMessage($this->getConfig()->get("not-enough-money-heal"));
                  }
                  break;
                case 1:
                  $economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
                  $money = $economy->myMoney($sender);
                  $moneyfeed = ($this->getConfig()->get("money-feed"));
                    if($money >= $moneyfeed){
                    $economy->reduceMoney($sender, $moneyfeed);
                    $sender->getHungerManager()->setFood(20);
                    $sender->getHungerManager()->setSaturation(20);
                    $sender->sendMessage($this->getConfig()->get("message-feed"));
                  } else {
                    $sender->sendMessage($this->getConfig()->get("not-enough-money-feed"));
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
