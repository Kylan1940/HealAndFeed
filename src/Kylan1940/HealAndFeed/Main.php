<?php

namespace Kylan1940\HealAndFeed;

use Kylan1940\HealAndFeed\Form\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use function file_exists;
use function rename;

class Main extends PluginBase implements Listener {

	const CONFIG_VERSION = 6;
	const PREFIX = "prefix";

	public function onEnable(): void {
		$this->updateConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	private function updateConfig(): void {
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

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
		if ($cmd->getName() == "heal") {
			if (!$sender->hasPermission("healandfeed-heal.command")) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.heal'));
				return false;
			}

		  if (isset($args[0])) {
				if (!$sender->hasPermission("healandfeed-healother.command")) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.healother'));
					return false;
				}

				$player = $this->getServer()->getPlayerExact($args[0]);
				if (!$player instanceof Player) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-player.found'));
					return false;
				}
			} else {
				if (!$sender instanceof Player) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('console-command.heal'));
					return false;
				}

				$player = $sender;
			}

			$this->heal($player);
		} elseif ($cmd->getName() == "healall") {
			if ($sender->hasPermission("healandfeed-healall.command")) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.healall'));
				return false;
			}

			if (empty($this->getServer()->getOnlinePlayers())) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-player.online'));
				return false;
			}

			$this->healAll();
			$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('message.healall'));
		} elseif ($cmd->getName() == "feed") {
			if ($sender->hasPermission("healandfeed-heal.command")) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.feed'));
				return false;
			}

			if (isset($args[0])) {
				if ($sender->hasPermission("healandfeed-feedother.command")) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.feedother'));
					return false;
				}

				$player = $this->getServer()->getPlayerExact($args[0]);
				if (!$player instanceof Player) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-player.found'));
					return false;
				}
			} else {
				if (!$sender instanceof Player) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('console-command.feed'));
					return false;
				}

				$player = $sender;
			}

			$this->feed($player);
		} elseif ($cmd->getName() == "feedall") {
			if ($sender->hasPermission("healandfeed-feedall.command")) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.feedall'));
				return false;
			}

			if (empty($this->getServer()->getOnlinePlayers())) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-player.online'));
				return false;
			}

			$this->feedAll();
			$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('message.feedall'));
		} elseif ($cmd->getName() == "healfeed") {
			if (!$sender instanceof Player) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('console-command.ui'));
				return false;
			}

			if (!$sender->hasPermission("healandfeed-ui.command")) {
				$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.ui'));
				return false;
			}

			$this->HealFeed($sender);
		}

		return true;
  }

	public function HealFeed(Player $sender): void {
		$form = new SimpleForm(function (Player $sender, int $result = null) {
			if ($result == "heal") {
				if ($sender->hasPermission("healandfeed-heal.command")) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.heal'));
					return;
				}

				$this->heal($sender);
			} elseif ($result == "feed") {
				if (!$sender->hasPermission("healandfeed-feed.command")) {
					$sender->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('no-permission.feed'));
					return;
				}

				$this->feed($sender);
			}
		});
		$form->setTitle($this->getConfig()->getNested('ui.title'));
		$form->addButton($this->getConfig()->getNested('ui.button.heal'), 1, $this->getConfig()->getNested('ui.button.image.heal'), "heal");
		$form->addButton($this->getConfig()->getNested('ui.button.feed'), 1, $this->getConfig()->getNested('ui.button.image.feed'), "feed");
		$form->sendToPlayer($sender);
	}

	private function heal(Player $player): void {
		$player->setHealth($player->getMaxHealth());
		$player->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('message.heal'));
	}

	private function healAll(): void {
		foreach ($this->getServer()->getOnlinePlayers() as $online) {
			$this->heal($online);
		}
	}

	private function feed(Player $player): void {
		$player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
		$player->getHungerManager()->setSaturation(20);
		$player->sendMessage($this->getConfig()->get(self::PREFIX) . $this->getConfig()->getNested('message.feed'));
	}

	private function feedAll(): void {
		foreach ($this->getServer()->getOnlinePlayers() as $online) {
			$this->feed($online);
		}
	}
}
