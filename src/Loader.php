<?php

namespace dhnnz\KillJackpot;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

/**
 * Class Loader
 * @package dhnnz\KillJackpot
 */
class Loader extends PluginBase implements Listener
{
    use SingletonTrait;

    /**
     * @inheritDoc
     */
    public function onEnable(): void
    {
        self::setInstance($this);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }

    /**
     * Summary of getItemReward
     * @param array $items
     * @return Item|null
     */
    public static function getItemReward(array $items): Item|null
    {
        $valid_items = [];

        foreach ($items as $item => $prob) {
            $prob_dec = filter_var($prob, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $prob_dec /= 100;
            $item_data = StringToItemParser::getInstance()->parse($item);

            if ($item_data !== null) {
                $valid_items[] = [
                    "name" => $item_data->getName(),
                    "probability" => $prob_dec
                ];
            }
        }

        $total_prob = 0;

        foreach ($valid_items as $item) {
            $total_prob += $item["probability"];
        }

        $rand_num = mt_rand(0, $total_prob * 100) / 100;
        $selected_item = null;

        foreach ($valid_items as $item) {
            if ($rand_num <= $item["probability"]) {
                $selected_item = $item["name"];
                break;
            }
            $rand_num -= $item["probability"];
        }

        return ($selected_item !== null) ? StringToItemParser::getInstance()->parse($selected_item) : null;
    }

    /**
     * Summary of onKill
     * @param EntityDamageEvent $entityDamageEvent
     * @return void
     */
    public function onKill(EntityDamageEvent $entityDamageEvent){
        $entity = $entityDamageEvent->getEntity();
        if (!$entityDamageEvent instanceof EntityDamageByEntityEvent || !$entityDamageEvent->getDamager() instanceof Player) {
            return;
        }

        /** @var Player $damager */
        $damager = $entityDamageEvent->getDamager();
        if ($entityDamageEvent->getFinalDamage() <= $entity->getHealth()) {
            return;
        }
        if (in_array($damager->getWorld()->getFolderName(), $this->getConfig()->get("disable_worlds"))) {
            return;
        }

        $entity->kill();
        $reward = $this::getItemReward($this->getConfig()->get("items"));
        if ($reward !== null && $damager->getInventory()->canAddItem($reward)) {
            $pos = $entity->getPosition();
            $damager->getWorld()->dropItem(new Vector3($pos->getX(), $pos->getY(), $pos->getZ()), $reward);
            $playerMessage = str_replace("%reward", $reward->getName(), $this->getConfig()->get("player.getreward.message"));
            $coloredMessage = TextFormat::colorize($playerMessage);
            $damager->sendMessage($coloredMessage);
        }
    }
}