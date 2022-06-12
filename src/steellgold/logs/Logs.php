<?php

namespace steellgold\logs;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\plugin\PluginBase;

class Logs extends PluginBase implements Listener {

	public Logs $instance;

	public Webhook $webhook;

	public function onEnable(): void {
		$this->instance = $this;


		if (!file_exists($this->getDataFolder() . "config.yml")) {
			$this->saveResource("config.yml", true);
			$this->getLogger()->alert("Please configure your webhook in the config.yml file!");
			$this->getServer()->shutdown();
			return;
		}

		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		$this->webhook = new Webhook($this->getConfig()->get("webhook-url"));
		if ($this->getConfig()->get("send-status", true)) {
			$msg = new Message();
			$msg->setContent("Connected.");
			$this->webhook->send($msg);
		}
	}

	public function onDrop(PlayerDropItemEvent $event) {
		$player = $event->getPlayer();
		$item = $event->getItem();
		if (!in_array($player->getName(), $this->getConfig()->get('players'))) return;
		if (!in_array($item->getId() . ":" . $item->getMeta(), $this->getConfig()->get('items'))) return;
		$msg = new Message();
		$msg->setUsername(
			$this->getConfig()->get("webhook-name") == "PLAYER_NAME" ? $player->getName() : $this->getConfig()->get("webhook-name")
		);
		if($this->getConfig()->get("webhook-avatar") !== null){
			$msg->setAvatarURL($this->getConfig()->get("webhook-avatar"));
		}
		$msg->setContent(str_replace([
			"{USERNAME}", "{ITEM_NAME}", "{ITEM_ID}", "{ITEM_META}", "{COUNT}", "{WORLD_NAME}"
		], [
			$player->getName(), $item->getName(), $item->getId(), $item->getMeta(), $item->getCount(), $player->getWorld()->getDisplayName()
		],
			$this->getConfig()->get("webhook-content"))
		);

		$this->webhook->send($msg);
	}

	public function onDisable(): void {
		if ($this->getConfig()->get("send-status", true)) {
			$msg = new Message();
			$msg->setContent("Disconnected.");
			$this->webhook->send($msg);
		}
	}
}