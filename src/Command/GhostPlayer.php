<?php

namespace davidglitch04\GhostPlayer\Command;

use davidglitch04\GhostPlayer\Loader;
use muqsit\fakeplayer\info\FakePlayerInfoBuilder;
use muqsit\fakeplayer\Loader as FakeplayerLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
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

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermission($sender, "ghostplayer.command.allow")){
            $uuid = $this->ghostplayer->getUuid();
            if (!isset($args[1])){
                $sender->sendMessage("Usage: /ghostplayer <spawn/player: skin> <random/string: name>");
                return;
            }
            if ($args[0] == "spawn" or $args[0] == "s"){
                if ($args[1] == "random" or $args[1] == "r"){
                    $username = $this->randomUserName();
                    $skin_data = stream_get_contents($this->ghostplayer->getResource("skin.rgba"));
                    $skin = new Skin("Standard_Custom", $skin_data);
                    GhostPlayer::SpawnPlayer($username, $uuid, $skin);
                    $sender->sendMessage("Create GhostPlayer Success!");
                }
            } else{
                $player = Server::getInstance()->getPlayerByPrefix($args[0]);
                if ($player == null){
                    $sender->sendMessage("Player skin not found!");
                    return;
                } elseif (Server::getInstance()->getPlayerByPrefix($args[1]) !== null){
                    $sender->sendMessage("There are already players with this name!");
                    return;
                } else{
                    GhostPlayer::SpawnPlayer($args[1], $uuid, $player->getSkin());
                    $sender->sendMessage("Create GhostPlayer Success!");
                }
            }
        }
    }

    private static function SpawnPlayer(string $username, string $uuid, Skin $skin): void{
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("FakePlayer");
        if($plugin instanceof FakeplayerLoader){
            $plugin->addPlayer(FakePlayerInfoBuilder::create()
                ->setUsername($username)
                ->setXuid("ghostplayerxuid")
                ->setUuid($uuid)
                ->setSkin($skin)
            ->build());
        }
    }

    private function randomUserName(): string{
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randstring = '';
            for ($i = 0; $i < 8; $i++) {
                $randstring .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randstring;
        }
    }
}
