<?php
namespace nn;

use pocketmine\scheduler\Task;

class ReBirthTask extends Task
{
	protected $plugin;
       protected $npc;
       protected $level;
       protected $name;
       protected $switch = 0;



	
	public function __construct(main $plugin,$npc,$level,$name){
		$this->plugin = $plugin;
             $this->npc = $npc;
             $this->level = $level;
             $this->name = $name;

	}

	public function onRun($currentTicks){

foreach($this->plugin->getServer()->getLevels() as $level){
if($level->getName() == $this->level){

foreach($level->getEntities() as $entity){

if($entity instanceof Npc){

$this->switch += $entity->getMyName() == $this->npc->getMyName() ? 1 : 0;


}
}


}
}

foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
$player->sendMessage("{$this->switch}");
}




if($this->switch == 0){
$this->plugin->spawnNpc($this->name);
}else{
$this->switch = 0;
}

       }


}
?>
