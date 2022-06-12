<?php

namespace steellgold\logs;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\plugin\PluginBase;

class Logs extends PluginBase implements Listener {
	public Logs $instance;

	public function onEnable() : void {
		$this->instance = $this;
		if(!file_exists($this->getDataFolder() . "config.yml")) {
			$this->saveResource("config.yml",true);
		}
	}

	public function onDrop(PlayerDropItemEvent $event){
		$event = $event->getPlayer();
	}
}