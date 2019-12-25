<?php
namespace nn\Task;

use pocketmine\scheduler\Task;
use nn\main;
use nn\Npc;

class ReBirthTask extends Task
{
	protected $plugin;
       protected $level;
       protected $name;
       protected $switch = 0;



	
	public function __construct(main $plugin,$level,$name){
		$this->plugin = $plugin;
             $this->level = $level;
             $this->name = $name;

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

/*foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
$player->sendMessage("{$this->switch}");
}*/
#test




if($this->switch == 0){
$this->plugin->spawnNpc($this->name);
$this->getHandler()->cancel();
}else{
$this->switch = 0;
}

       }


}
?>
