<?php

namespace davidglitch04\GhostPlayer;

use davidglitch04\GhostPlayer\Command\GhostPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use Ramsey\Uuid\Uuid;

class Loader extends PluginBase{
    
    protected function onEnable(): void
    {
        $this->initDepends();
        $this->getServer()->getCommandMap()->register("ghostplayer", new GhostPlayer($this));
    }

    protected function initDepends(): void{
        $fakeplayer = Server::getInstance()->getPluginManager()->getPlugin("FakePlayer");
        if($fakeplayer == null){
            Server::getInstance()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->notice("You need download plugin FakePlayer at https://github.com/Muqsit/FakePlayer");
        }
    }

    public function getUuid(){
        return Uuid::uuid4();
    }
}
