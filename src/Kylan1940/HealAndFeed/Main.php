<?php

namespace Kylan1940\HealAndFeed;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};

class Main extends PluginBase implements Listener {

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if($sender instanceof Player){
            if($sender->hasPermission("heal.command")){
                if($cmd->getName() == "heal"){
                    $sender->setHealth($sender->getMaxHealth());
                    $sender->sendMessage($this->getConfig()->get("message-heal"));
                }
            } else {
              $sender->sendMessage($this->getConfig()->get("no-permission"));
            }
            if($sender->hasPermission("feed.command")){
                if($cmd->getName() == "feed"){
                    $sender->setFood(20);
                    $sender->setSaturation(20);
                    $sender->sendMessage($this->getConfig()->get("message-feed"));
                }
            } else {
              $sender->sendMessage($this->getConfig()->get("no-permission"));
            }
            if($sender->hasPermission("hfui.command")){
              if($cmd->getName() == "healfeed"){
                $this->HealFeed($sender);
              }
            } else {
              $sender->sendMessage($this->getConfig()->get("no-permission"));
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