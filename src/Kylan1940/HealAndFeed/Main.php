<?php

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};

class Main extends PluginBase implements Listener {
  
  public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
        
        // Check config
        if($this->getConfig()->get("config-ver") != 2)
        {
            $this->getLogger()->info("HealAndFeed's config is NOT up to date. Please delete the config.yml and restart the server or the plugin may not work properly.");
        }
  }
   
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if($sender instanceof Player){
          $heal = $this->getConfig()->get("money-heal");
          $feed = $this->getConfig()->get("money-feed");
                if($cmd->getName() == "heal"){
                  if ($sender -> hasPermission("healandfeed-heal.command")) {
                    $sender->setHealth($sender->getMaxHealth());
                    BedrockEconomyAPI::legacy()->subtractFromPlayerBalance($player->getName(), (int) $heal);
                    $sender->sendMessage($this->getConfig()->get("message-heal")); 
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-heal"));
                  }
                }
                if($cmd->getName() == "feed"){
                  if ($sender -> hasPermission("healandfeed-feed.command")) {
                    $sender->getHungerManager()->setFood(20);
                    $sender->getHungerManager()->setSaturation(20);
                    BedrockEconomyAPI::legacy()->subtractFromPlayerBalance($player->getName(), (int) $feed);
                    $sender->sendMessage($this->getConfig()->get("message-feed")); 
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-feed"));
                  }
                } 
                if($cmd->getName() == "healfeed"){
                  if ($sender -> hasPermission("healandfeed-ui.command")) {
                    $this->HealFeed($sender);
                  } else {
                    $sender->sendMessage($this->getConfig()->get("no-permission-ui"));
                  }
                } 
        } else {
          $sender->sendMessage($this->getConfig()->get("only-ingame"));
        }
        return true;
    }
    
  public function HealFeed($sender){
      BedrockEconomyAPI::legacy()->getPlayerBalance(
			$player->getName(),
			ClosureContext::create(
				function (?int $balance) use ($player): void {
				  $form = new SimpleForm(function (Player $sender, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                  $heal = $this->getConfig()->get("money-heal");
                    if ($sender -> hasPermission("healandfeed-heal.command")) {
                      $sender->setHealth($sender->getMaxHealth());
                      BedrockEconomyAPI::legacy()->subtractFromPlayerBalance($player->getName(), (int) $heal);
                      $sender->sendMessage($this->getConfig()->get("message-heal")); 
                    } else {
                      $sender->sendMessage($this->getConfig()->get("no-permission-heal"));
                    }
                  break;
                case 1:
                  $feed = $this->getConfig()->get("money-feed");
                    if ($sender -> hasPermission("healandfeed-feed.command")) {
                      $sender->getHungerManager()->setFood(20);
                      $sender->getHungerManager()->setSaturation(20);
                      BedrockEconomyAPI::legacy()->subtractFromPlayerBalance($player->getName(), (int) $feed);
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
				},
			)
		);
    }

}