<?php

namespace davidglitch04\GhostPlayer\Command;

use davidglitch04\GhostPlayer\Loader;
use muqsit\fakeplayer\info\FakePlayerInfoBuilder;
use muqsit\fakeplayer\Loader as FakeplayerLoader;
use muqsit\fakeplayer\network\FakePlayerNetworkSession;
use muqsit\fakeplayer\network\listener\ClosureFakePlayerPacketListener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Language;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;

class GhostPlayer extends Command implements PluginOwned {

    protected Loader $ghostplayer;

    public function __construct(Loader $ghostplayer)
    {
        $this->ghostplayer = $ghostplayer;
        parent::__construct("ghostplayer", "Create GhostPlayer", "Usage: /ghostplayer <player: skin> <string: name>", ['gp', 'gplayer']);
        $this->setPermission("ghostplayer.command.allow");
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->ghostplayer;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($this->testPermission($sender, "ghostplayer.command.allow")){
            if (!isset($args[1])){
                $sender->sendMessage("Usage: /ghostplayer <player: skin> <string: name>");
                return;
            }
            $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            if ($player == null){
                $sender->sendMessage("Player skin not found!");
                return;
            } elseif (Server::getInstance()->getPlayerByPrefix($args[1]) !== null){
                $sender->sendMessage("There are already players with this name!");
                return;
            } else{
                $plugin = Server::getInstance()->getPluginManager()->getPlugin("FakePlayer");
                if($plugin instanceof FakeplayerLoader){
                    $plugin->addPlayer(FakePlayerInfoBuilder::create()
                        ->setUsername($args[1])
                        ->setXuid("ghostplayerxuid")
                        ->setUuid($this->ghostplayer->getUuid())
                        ->setSkin($player->getSkin())
                    ->build());
                    }
                    $sender->sendMessage("Create GhostPlayer Success!");
                }
            }
        }
    }
}
