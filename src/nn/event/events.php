<?php

namespace nn\event;

use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
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


public function reward(EntityDamageByEntityEvent $event){

$damage=$event->getBaseDamage();
$body=$event->getEntity();
$damager=$event->getDamager();

if($damager instanceof Player and $body instanceof Npc){
if($body->getHealth() <= $damage){

$bodyname=$body->getMyName();

$config = new Config(self::$plugin->getDataFolder()."data/"."{$bodyname}/"."config.yml",Config::YAML);

$money = $config->get("reward-money");
$item = $config->get("reward-items");
$cmd = str_replace("@p",$damager->getName(), $config->get("reward-cmds"));

$level = $config->get("level");
$time = $config->get("ReBirthTime");
             
self::$plugin->getScheduler()->scheduleRepeatingTask(new ReBirthTask(self::$plugin,$level,$bodyname), $time*20);


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


unset($bodyname,$config,$money,$item,$cmd,$level,$name,$time);
}
}
unset($damage,$body,$damager);
}







}