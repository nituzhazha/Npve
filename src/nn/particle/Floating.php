<?php

namespace nn\particle;

use pocketmine\entity\Entity;
use pocketmine\entity\DataPropertyManager;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\math\Vector3;
use pocketmine\Player;
use nn\main;

class Floating
{

    const NETWORK_ID = 10;
   
    public $plugin;
    public $point;
    public $name;
    public $vector;
    public $id;
   


    public function __construct(main $plugin, Vector3 $vector,$point,$id)
    {
        $this->plugin = $plugin;
        $this->vector = $vector;
        $this->point = $point;
        $this->id = $id;
    }



public function setName($time){
$this->name = "§4刷怪点"."\n§2{$this->point}"."\n§5刷新时间{$time}s";
}




    public function set(Player $player){
		$pk = new AddActorPacket();
		$pk->entityRuntimeId = $this->id;
		$pk->type = self::NETWORK_ID;
		$pk->position = $this->vector;
             $property = new DataPropertyManager();
             $property->setFloat(Entity::DATA_SCALE,0.01);
             $property->setByte(Entity::DATA_ALWAYS_SHOW_NAMETAG,true);
             $property->setString(Entity::DATA_NAMETAG, $this->name);
		$pk->metadata = $property->getAll();

if($player == null){
return;
}else{
		$player->dataPacket($pk);
}
	}


}