<?php

declare(strict_types=1);

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};

class Main extends PluginBase implements Listener {
	
	public function onEnable() : void {
        $this->saveDefaultConfig();
        $this->reloadConfig();
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
      }
  
  public function onCommand(CommandSender $sender,Command $cmd,string $label,array $args) : bool{
      if ($sender instanceof Player) {
        if ($cmd->getName() == "feed") {
          $sender->getHungerManager()->setFood(20);
          $sender->getHungerManager()->setSaturation(20);
          $sender->sendMessage($this->getConfig()->get("message-feed"));
          }
        if ($cmd->getName() == "heal") {
          $sender->setHealth($sender->getMaxHealth());
          $sender->sendMessage($this->getConfig()->get("message-heal")); 
          }
        if ($cmd->getName() == "healfeed") {
          $this->HealFeed($sender);
          }
      } else {
        $sender->sendMessage("This command is only in game!");
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
                    $sender->setHealth($sender->getMaxHealth());
                    $sender->sendMessage($this->getConfig()->get("message-heal"));
                  break;
                case 1:
                    $sender->setFood(20);
                    $sender->setSaturation(20);
                    $sender->sendMessage($this->getConfig()->get("message-feed"));
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