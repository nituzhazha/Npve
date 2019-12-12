<?php

namespace nn;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\event\level\ChunkUnloadEvent;
use nn\Utils\Converter;
use nn\ReBirthTask;
use pocketmine\Server;
use pocketmine\event\player\PlayerMoveEvent;

class main extends PluginBase implements Listener{
	
	public $npc = null;
	public $level = null;
	public $configdir = null;
	public $single = null;

	
	public function onEnable(){

		@mkdir($this->getDataFolder());
		
if(!is_dir($this->getDataFolder()."data")){
@mkdir($this->getDataFolder()."data");
}

if(!file_exists($this->getDataFolder()."skin.png")){
$this->saveResource("skin.png");
}


		$this->single = $this->getDataFolder()."data/";
		

		 
		

for($i=2;$i<count(scandir($this->getDataFolder()."data/"));$i++){

if(count(scandir($this->getDataFolder()."data/"))<=2){
$this->getLogger()->info("无配置文件加载");
}else{
$this->spawnNpc((string)scandir($this->getDataFolder()."data/")[$i]);
$this->RBT((string)scandir($this->getDataFolder()."data/")[$i]);

		}

}
		

       Entity::registerEntity(Npc::class, true);

		$this->getServer()->getPluginManager()->registerEvents($this, $this);


	}
	
	
	
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		
		
		
		if($command->getName()=="npve"){

if(!isset($args[0])){
		$sender->sendMessage("你没有设置方法");
		return false;
	}

if($args[0] == "set"){
	
	if(!isset($args[1])){
		$sender->sendMessage("你没有设置点名");
		return false;
	}
	
	if(!is_dir($this->single."{$args[1]}")){
@mkdir($this->single."{$args[1]}");
}
     
	 $pz = new Config($this->single."{$args[1]}/"."config.yml",Config::YAML,[
	       "name" => "$args[1]",
		   "level" => $sender->getLevel()->getName(),
		   "health" => 20,
           "damage" => 1,
		   "speed" => 0.1,
		   "damage-distance" => 1,
		   "size" =>  1,
           "position" => array(
           "x" =>(int)$sender->getX(),
           "y" =>(int)$sender->getY(),
           "z" =>(int)$sender->getZ())
		   ]
		   );#initial data

		$pz->save();

		$this->spawnNpc($args[1]);
}

		}
return true;
	}


	
	
	public function spawnNpc($to){

              $total = $to;

if(!file_exists($this->single."{$total}/"."config.yml") or !is_dir($this->single."{$total}")) return;


		$config = new Config($this->single."{$total}/"."config.yml",Config::YAML);

		$skinResource = $this->single."{$total}/"."skin.png";


if(!file_exists($this->single."{$total}/"."skin.png")){
$skinResource = $this->getDataFolder()."skin.png";
}


$skin =
Converter::getPngSkin($skinResource);

		 $level = $config->get("level");
		 $name = $config->get("name");
		 $health = $config->get("health");
		 $damage = $config->get("damage");
		 $speed = $config->get("speed");
		 $damagedistance = $config->get("damage-distance");
		 $size = $config->get("size");
		 $pos = $config->get("position");
	

if($pos == null) return;

$poss = new Vector3($pos["x"], $pos["y"], $pos["z"]);

foreach($this->getServer()->getLevels() as $levels){
if($levels->getName() == $level){


		$npc = new Npc($levels,Entity::createBaseNBT($poss),$skin,$poss,$this->getServer(),$name,$damage,$speed,$damagedistance, $this);


        $npc->setScale($size);
        $npc->setMaxHealth($health);
        $npc->setHealth($health);

$npc->spawnToAll();
		$this->npc = $npc;
}}

	}

public function RBT($total){

if(!file_exists($this->single."{$total}/"."config.yml") or !is_dir($this->single."{$total}")) return;

		$config = new Config($this->single."{$total}/"."config.yml",Config::YAML);
              $level = $config->get("level");
		 $name = $config->get("name");
             

$this->getScheduler()->scheduleRepeatingTask(new ReBirthTask($this,$this->npc,$level,$name), 40);

}


	}



