<?php

namespace nn\particle;

use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use nn\main;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\particle\HeartParticle;


class RebirthEffect{

public $plugin;

    public function __construct(main $plugin)
    {
        $this->plugin = $plugin;
    }


   public function setEffect($who){

$config = new Config($this->plugin->getDataFolder()."data/"."{$who}/"."config.yml",Config::YAML);

$pos = $config->get("position");

$poss = new Vector3($pos["x"], $pos["y"], $pos["z"]);

$level = $config->get("level");

foreach($this->plugin->getServer()->getLevels() as $levels){
if($levels->getName() == $level){

$levels->addSound(new AnvilUseSound($poss));


for($i=1;$i<=4;$i++){

$poss = new Vector3($pos["x"], $pos["y"]+$i/2, $pos["z"]);

$levels->addParticle(new HeartParticle($poss,5*$i));

}

}
}




}







}