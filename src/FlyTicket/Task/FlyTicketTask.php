<?php

namespace FlyTicket\Task;

use _64FF00\PureChat\PureChat;
use FlyTicket\Main;
use FlyTicket\Manager\FlyTicketManager;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class FlyTicketTask extends Task
{
    public $time;
    public $player;

    public function __construct(Player $player, int $time){
        $this->time = $time;
        $this->player = $player;
    }
    public function onRun(): void
    {
        $player = $this->player;
        if ($player instanceof Player) {
            if ($player->isOnline()) {
                $this->time--;
                $tarih = gmdate("i:s", $this->time);
                $exp = explode(":", $tarih);
                $dk = (int)$exp[0];
                $saniye = (int)$exp[1];
                if ($dk < 10) str_replace(["0"], [""], $dk);
                $exp = explode(":", $tarih);
                $dk = (int)$exp[0];
                $saniye = (int)$exp[1];
                if ($dk < 10) str_replace(["0"], [""], $dk);

                if ($exp[0] === 00) {
                    $format = "§f" . $saniye . " §bsaniye";
                } else {
                    $format = "§f" . $dk . " §bdakika §f" . $saniye . " §bsaniye";
                }
                if (!$player->getAllowFlight()) {
                    $player->setAllowFlight(true);
                }
                $cfgformat = str_replace(["{dakika}", "{saniye}"], [$dk, $saniye], Main::$config->get("Ucus-Mesaji"));
                $player->sendActionBarMessage($cfgformat);
                $second = str_replace("{saniye}", $this->time, Main::$config->get("Uyarı-Title"));
                $second_array = [60, 10, 5, 4, 3, 2, 1];
                if (in_array($this->time, $second_array)) {
                    $player->sendTitle($second);
                    $player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("note.pling", $player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), 2.0, 2.0));
                }
                if ($this->time === 0) {
                    $player->setAllowFlight(false);
                    if ($player->isFlying()) $player->setFlying(false);
                    unset(FlyTicketManager::$array[$player->getName()]);
                    $player->sendTitle("§cUçuş Belgesi bitti.");
                    $purechat = Server::getInstance()->getPluginManager()->getPlugin("PureChat");
                    $format = $purechat->getNametag($player);
                    $player->setNameTag($format);
                    $player->teleport($player->getWorld()->getSafeSpawn());
                    $this->getHandler()->cancel();
                }
            } else {
                $this->getHandler()->cancel();

            }
        }
    }
}