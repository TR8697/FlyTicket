<?php

namespace FlyTicket\Form;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use FlyTicket\Main;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use onebone\economyapi\EconomyAPI;

class FlyTicketForm extends CustomForm
{

    /**
     * @param Player $player
     */
    public function __construct(\pocketmine\player\Player $player)
    {
        $title = Main::$config->get("Form-Title");
        $price = Main::$config->get("Fiyat");
        $content = Main::$config->get("Content");
        $min = Main::$config->get("Min");
        $max = Main::$config->get("Max");
        $price_info = str_replace("{fiyat}", $price, Main::$config->get("PriceText"));
        $money = str_replace("{paran}", EconomyAPI::getInstance()->myMoney($player), Main::$config->get("Paran"));
        $label = $content.$money.$price_info;
        parent::__construct($title,
        [
            new Label("info", $label),
            new Slider("minute", "Dakika Seçin", $min, $max),
        ], function (Player $player, CustomFormResponse $response):void {
                $ecoapi = EconomyAPI::getInstance();
                $minute = $response->getFloat("minute");
                $price = $minute * Main::$config->get("Fiyat");
                if ($ecoapi->myMoney($player) >= $price) {
                    $second = $minute * 60;
                    $item_name = str_replace("{dakika}", $minute, Main::$config->get("ItemName"));
                    $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)->setCustomName("§r" . $item_name)->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(0), 10));
                    $item->getNamedTag()->setInt("second", $second);
                    $item->getNamedTag()->setString("date", date("d-m-Y-H-i-s-"));
                    if ($player->getInventory()->canAddItem($item)) {
                        $ecoapi->reduceMoney($player, $price);
                        $message = str_replace(["{toplamtutar}", "{dakika}"], [$price, $minute], Main::$config->get("Mesaj"));
                        $player->sendMessage($message);
                        $player->getInventory()->addItem($item);
                    } else $player->sendMessage("§cEnvanterinizde yer yok!");
                } else $player->sendMessage("§cParan yetersiz!");
            });
    }
}