<?php
namespace nn;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use nn\Utils\Converter;
use nn\event\events;
use nn\Task\FloatingTask;
use nn\particle\Floating;
use pocketmine\Server;
class main extends PluginBase{
	
	public $npc = null;
	public $level = null;
	public $configdir = null;
	public $single = null;
        public $id = 1000000;
	
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
$this->Floating((string)scandir($this->getDataFolder()."data/")[$i]);


		}
}
		
       Entity::registerEntity(Npc::class, true);
       events::initial($this);
     
$this->getLogger()->info("本插件由nitu一人制作");
$this->getLogger()->info("作者QQ1010340249");
$this->getLogger()->info("已开源禁止盗卖");
$this->getLogger()->info("github项目链接:https://github.com/nituzhazha/Npve");
	}
	
	
	
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		
		
		
		if($command->getName()=="npve"){
if(!isset($args[0])){
		$sender->sendMessage("你没有设置方法");
		return false;
	}

if($sender->isOp()){

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
                   "BiggestLimit" => 12,
                   "SmallestLimit" => 5,
		   "size" =>  1,
           "position" => array(
           "x" =>(int)$sender->getX(),
           "y" =>(int)$sender->getY(),
           "z" =>(int)$sender->getZ()),
           "ReBirthTime" => 2,
           "reward-money" => 10,
           "reward-items" => [],
           "reward-cmds" =>[]
           
		   ]
		   );#initial data
		$pz->save();
		 $this->spawnNpc($args[1]);
                 $this->Floating($args[1]);
             
}
		}else{
$sender->sendMessage("§4你没有权限使用");
}


}
return true;
	}
	

public function getGeometryName($path){
		
		$array = new Config($path, Config::JSON, []);
		$array = $array->getAll();
		
		foreach($array as $name => $data){
			
			return $name;#返回json文件的第一个key
		}
	}

	
	public function spawnNpc($to){
              $total = $to;
if(!file_exists($this->single."{$total}/"."config.yml") or !is_dir($this->single."{$total}")) return;
		$config = new Config($this->single."{$total}/"."config.yml",Config::YAML);

		$skinResource = $this->single."{$total}/"."skin.png";

if(!file_exists($this->single."{$total}/"."skin.png")){
$skinResource = $this->getDataFolder()."skin.png";
}

$geometryName = "";

$geometryData = "";



if(file_exists($this->single."{$total}/"."model.png") and file_exists($this->single."{$total}/"."model.json")){

$skinResource = $this->single."{$total}/"."model.png";

$jsonPath = $this->single."{$total}/"."model.json";

$geometryName =$this->getGeometryName($jsonPath);

$geometryData = file_get_contents($jsonPath);

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
                 $BiggestLimit = $config->get("BiggestLimit");
                 $SmallestLimit = $config->get("SmallestLimit");
                 
	
if($pos == null) return;
$poss = new Vector3($pos["x"], $pos["y"], $pos["z"]);
foreach($this->getServer()->getLevels() as $levels){
if($levels->getName() == $level){
		$npc = new Npc($levels,Entity::createBaseNBT($poss),$skin,$poss,$this->getServer(),$name,$damage,$speed,$damagedistance,$this, $geometryName, $geometryData,$BiggestLimit, $SmallestLimit);
        $npc->setScale($size);
        $npc->setMaxHealth($health);
        $npc->setHealth($health);
$npc->spawnToAll();
		$this->npc = $npc;
}}
	}

public function Floating($total){

if(!file_exists($this->single."{$total}/"."config.yml") or !is_dir($this->single."{$total}")) return;

$config = new Config($this->single."{$total}/"."config.yml",Config::YAML);
 $pos = $config->get("position");
$poss = new Vector3($pos["x"], $pos["y"]+2.5, $pos["z"]);
 $name = $config->get("name");
$time = $config->get("ReBirthTime");
$level = $config->get("level");
$floating = new Floating($this,$poss,$name,$this->id);

$this->id++;
$floating->setName($time);
$this->getScheduler()->scheduleRepeatingTask(new FloatingTask($this,$floating,$name,$time,$level), 20);



}


	}
