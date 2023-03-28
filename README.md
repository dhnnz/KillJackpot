## KillJackpot
 KillJackpot is a PocketMine-MP server plugin that rewards players when they kill certain monsters. Rewards are given in the form of pre-determined items specified in the plugin's configuration.

## Configuration
  The config.yml file contains the following configuration options:
# player.getreward.message
  The message that will be displayed to the player when they receive a reward. The message can contain a placeholder %reward, which will be replaced with the name of the reward item.

Example usage:

```yaml
player.getreward.message: "&aKamu mendapatkan &e%reward"
```
# items
A list of reward items and their chance of appearing. The item name must be spelled correctly and match the in-game name of the item.

Example usage:

```yaml
items:
  Iron_Ingot: 60%
  Gold_Ingot: 50
  Diamond_Ingot: 30
```
In the example above, the chance of Iron Ingot appearing is 60%, Gold Ingot is 50%, and Diamond Ingot is 30%.

# disable_worlds
A list of world names where the plugin will not have any effect.

Example usage:

```yaml
disable_worlds:
- world1
```
In the example above, the world named "world1" will not be affected by the plugin.

## Lisensi
KillJackpot is licensed under the **[General Public License v2.1](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt)**.