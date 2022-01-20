<?php

declare(strict_types=1);

namespace Kylan1940\HealAndFeed;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use Kylan1940\HealAndFeed\Form\{Form, SimpleForm};
use Kylan1940\HealAndFeed\commands\{HealCommand, FeedCommand, UICommand};

class Main extends PluginBase {
	
	public function onEnable() : void {
        $this->saveDefaultConfig();
        $this->reloadConfig();
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
      }
}