<?php

namespace nn;

use pocketmine\Player;
use pocketmine\entity\Skin;
use pocketmine\entity\Human;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class Npc extends Human
{

    const NETWORK_ID = 63;
    public $server;
    public $pos;
    public $name;
    public $damage;
    public $speed;
    public $damagedistance;
    public $pp;
    public $plugin;
    public $BiggestLimit;
    public $SmallestLimit;
   


    public function __construct(Level $level, CompoundTag $nbt,String $skindata ,$pos, $se, $name, $damage, $speed, $damagedistance,$plugin,$geometryName,$geometryData,$BiggestLimit,$SmallestLimit)
    {
        $this->server = $se;
        $this->pos = $pos;
        $this->name = $name;
        $this->damage = $damage;
        $this->speed = $speed;
        $this->damagedistance = $damagedistance;
        $this->BiggestLimit = $BiggestLimit;
        $this->SmallestLimit = $SmallestLimit;
        $this->plugin = $plugin;
        

        $this->setSkin(new Skin(
            "by rookie soil",
            $skindata,
"",
$geometryName,
$geometryData
        ));

        parent::__construct($level, $nbt);
    }


    public function onUpdate(int $currentTick): bool
    {
        $parent = parent::onUpdate($currentTick);
//返回true or false来判断是否进行计时器的运转

        $e = $this->getPosition();

        $x = 0;
        $y = 0;
        $z = 0;

        $ox = $this->pos->getX();
        $oy = $this->pos->getY();
        $oz = $this->pos->getZ();

        $ex = $e->getX();
        $ey = $e->getY();
        $ez = $e->getZ();

      

        foreach ($this->server->getOnlinePlayers() as $key => $pl) {
            if ($pl->getPosition()->distance(new Vector3($ex, $ey, $ez)) <= $this->SmallestLimit) {
                $this->pp = $pl;
            }
        }

        $player = $this->pp;

        $this->setNameTag("$this->name\nHealth:{$this->getHealth()}");


        if ($player == null) return false;

        $px = $player->getX();
        $py = $player->getY();
        $pz = $player->getZ();


        if ($player->getPosition()->distance(new Vector3($ox, $oy, $oz)) <= $this->BiggestLimit) {

            if ($px > $ex) {
                $x = $this->speed;
            }
            if ($px < $ex) {
                $x = -$this->speed;
            }
            if ($px == $ex) {
                $x = 0;
            }

            if ($pz > $ez) {
                $z = $this->speed;
            }
            if ($pz < $ez) {
                $z = -$this->speed;
            }
            if ($pz == $ez) {
                $z = 0;
            }
        } else {

            if ($ex > $ox) {
                $x = -$this->speed;
            }
            if ($ex < $ox) {
                $x = $this->speed;
            }

            if ($ez > $oz) {
                $z = -$this->speed;
            }
            if ($ez < $oz) {
                $z = $this->speed;
            }

            if ($ez == $oz) {
                $z = 0;
            }
            if ($ex == $ox) {
                $x = 0;
            }

        }

        $poo = new Vector3($this->getX(), $this->getY(), $this->getZ() + 0.3);
        $poos = new Vector3($this->getX(), $this->getY(), $this->getZ() - 0.3);
        if ($this->getLevel()->getBlock($poo)->getId() != 0 or $this->getLevel()->getBlock($poos)->getId() != 0) {
            $y = 0.5;
        }

        $p3 = new Vector3($this->getX() + 0.3, $this->getY(), $this->getZ());
        $p4 = new Vector3($this->getX() - 0.3, $this->getY(), $this->getZ());
        if ($this->getLevel()->getBlock($p3)->getId() != 0 or $this->getLevel()->getBlock($p4)->getId() != 0) {
            $y = 0.5;
        }


        $y2 = $py - $ey;
        $x2 = $px - $ex;
        $z2 = $pz - $ez;
        $atn = atan2($z2, $x2);
        $this->setRotation(rad2deg($atn - M_PI_2), rad2deg(-atan2($y2, sqrt($x2 ** 2 + $z2 ** 2))));
        unset($x2, $y2, $z2, $atn);


        $this->move($x, $y, $z);


        if ($player->getPosition()->distance(new Vector3($ex, $ey, $ez)) <= $this->damagedistance) {
            $damageEvent = new EntityDamageByEntityEvent($this, $player, EntityDamageByEntityEvent::CAUSE_ENTITY_ATTACK, $this->damage);
            $player->attack($damageEvent);
        }


        unset($x, $y, $z, $player);


        return $parent;
    }

public function getMyName(){
return $this->name;
}

public function setSkin(Skin $skin) : void{
    $this->skin = $skin;
    $this->skin->debloatGeometryData();
}



public function sendSpawnPacket(Player $player) : void{

			$pk = new PlayerListPacket();
			$pk->type = PlayerListPacket::TYPE_ADD;
			$pk->entries = [PlayerListEntry::createAdditionEntry($this->uuid, $this->id, $this->getName(),$this->skin)];
			$player->dataPacket($pk);
		

		$pk = new AddPlayerPacket();
		$pk->uuid = $this->getUniqueId();
		$pk->username = $this->getName();
		$pk->entityRuntimeId = $this->getId();
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->item = $this->getInventory()->getItemInHand();
		$pk->metadata = $this->propertyManager->getAll();
		$player->dataPacket($pk);

		$this->sendData($player, [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getNameTag()]]);

		$this->armorInventory->sendContents($player);

		
			$pk = new PlayerListPacket();
			$pk->type = PlayerListPacket::TYPE_REMOVE;
			$pk->entries = [PlayerListEntry::createRemovalEntry($this->uuid)];
			$player->dataPacket($pk);
		
	}
    public function saveNBT(): void
    {
    }

}