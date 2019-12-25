<?php
namespace nn\Task;

use pocketmine\scheduler\Task;
use nn\main;
use nn\Npc;

class FloatingTask extends Task
{
	protected $plugin;
       protected $floating;
       protected $time;
       protected $deltime;
       protected $level;
       protected $switch;
 
	
	public function __construct(main $plugin,$floating,$name,$time,$level){
		$this->plugin = $plugin;
             $this->floating = $floating;
             $this->name = $name;
             $this->time = $time;
             $this->deltime = $time;
             $this->level = $level;
            
	}

	public function onRun($currentTicks){


foreach($this->plugin->getServer()->getLevels() as $level){
if($level->getName() == $this->level){

foreach($level->getEntities() as $entity){

if($entity instanceof Npc){

$this->switch += $entity->getMyName() == $this->name ? 1 : 0;


}
}


}
}







if($this->switch == 1){
$this->switch = 0;
$this->floating->setName(0);
$this->deltime = $this->time;
}else{
$this->deltime--;
$this->floating->setName($this->deltime);
}

if($this->deltime == 0){
$this->deltime = $this->time;
}



foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
$this->floating->set($player);
}


       }


}
?>
