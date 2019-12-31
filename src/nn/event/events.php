<?php

namespace nn\event;

use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\level\ChunkUnloadEvent;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\command\ConsoleCommandSender;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\Item;
use nn\Npc;
use nn\Task\ReBirthTask;
use pocketmine\Player;

class events implements Listener{

static public $plugin;

static public function initial(Plugin $plugin){
self::$plugin = $plugin;
$plugin->getServer()->getPluginManager()->registerEvents(new events(),$plugin);
}

public function rebirth(EntityDeathEvent $event){

$body=$event->getEntity();

if($body instanceof Npc){

$bodyname=$body->getMyName();

$config = new Config(self::$plugin->getDataFolder()."data/"."{$bodyname}/"."config.yml",Config::YAML);

$level = $config->get("level");
$time = $config->get("ReBirthTime");

self::$plugin->getScheduler()->scheduleRepeatingTask(new ReBirthTask(self::$plugin,$level,$bodyname), $time*20);


if($body->getLastDamageCause() instanceof EntityDamageByEntityEvent){

$ev = $body->getLastDamageCause();

$damager=$ev->getDamager();

$money = $config->get("reward-money");
$item = $config->get("reward-items");
$cmd = str_replace("@p",$damager->getName(), $config->get("reward-cmds"));

if($damager instanceof Player){

for($i=0;$i<count($item);$i++){
$emm=explode(":",$item[$i]);
$item2 = new Item($emm[0],$emm[1]);
$item3 = $item2->setCount($emm[2]);
$damager->getInventory()->addItem($item3);
}

for($o=0;$o<count($cmd);$o++){
Server::getInstance()->dispatchCommand(new ConsoleCommandSender(),$cmd[$o]);
}

EconomyAPI::getInstance()->addMoney($damager, $money);

$damager->sendMessage("§3您击杀§4{$bodyname}§3的奖励已经发送至您的背包");


}

unset($ev,$damager,$money,$item,$cmd);
}






unset($bodyname,$level,$time);
}
unset($body);
}





public function chunkUnload(ChunkUnloadEvent $ev){
foreach(self::$plugin->getServer()->getLevels() as $level){

foreach($level->getEntities() as $entity){

if($entity instanceof Npc){

if($entity->level === $ev->getLevel() and $entity->chunk === $ev->getChunk()){
			$ev->setCancelled(true);
		}

}
}
}
unset($ev);
	}





}