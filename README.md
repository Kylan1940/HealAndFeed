[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/P5P1DB75T)
# Description
Heal and Feed Commands Plugin For Minecraft Java Server. If you are have a question, please make a [issues](https://github.com/Kylan1940/HealAndFeed/issues/new)

# Changelogs [1.2.0]
>- Added cooldown and bypass permission
>- Changed Configuration

# Features
>- Simple
>- Configuration
>- Can heal and feed other player
>- Heal All and Feed All online player(s)
>- GUI Menu
>- Console Support
>- Cooldown
>- Compatible with 7 Server Software (Paper, Spigot, CraftBukkit, Folia, Purpur, Pufferfish, Leaves)
>- Pay for heal and feed (soon)

# Download
| Version | JAR                                                                                                | Minecraft Support | Software Support                                              |
|---------|----------------------------------------------------------------------------------------------------|-------------------|---------------------------------------------------------------|
| 1.2.0   | [DOWNLOAD](https://github.com/Kylan1940/HealAndFeed/releases/download/1.2.0/HealAndFeed-1.2.0.jar) | 1.21 - 26.0+      | Paper, Spigot, CraftBukkit, Folia, Purpur, Pufferfish, Leaves |
| 1.1.0   | [DOWNLOAD](https://github.com/Kylan1940/HealAndFeed/releases/download/1.1.0/HealAndFeed-1.1.0.jar) | 1.21 - 26.0+      | Paper, Spigot, CraftBukkit, Folia, Purpur, Pufferfish, Leaves |
| 1.0.0   | [DOWNLOAD](https://github.com/Kylan1940/HealAndFeed/releases/download/1.0.0/HealAndFeed-1.0.0.jar) | 1.21 - 26.0+      | Paper, Spigot, CraftBukkit, Folia, Purpur, Pufferfish, Leaves |

# Installation
1. Download the [plugin](https://github.com/Kylan1940/HealAndFeed/releases/download/1.2.0/HealAndFeed-1.2.0.jar)
2. Put plugin to **plugins** file
3. Restart the server

# Permission
| Command        | Description          | Permission                    | Default  | Console Support | Aliases                    |
|----------------|----------------------|-------------------------------|----------|-----------------|----------------------------|
| /heal          | Heal yourself        | healandfeed-heal.command      | OP       | NO              | NONE                       |
| /heal <player> | Heal other player    | healandfeed-healother.command | OP       | YES             | NONE                       |
| /healall       | Heal all player(s)   | healandfeed-healall.command   | OP       | YES             | NONE                       |
| /feed          | Feed yourself        | healandfeed-feed.command      | OP       | NO              | NONE                       |
| /feed <player> | Feed other player    | healandfeed-feedother.command | OP       | YES             | NONE                       |
| /feedall       | Feed all player(s)   | healandfeed-feedall.command   | OP       | YES             | NONE                       |
| /healfeed      | GUI Menu             | NONE                          | YES      | NO              | /haf, /healmenu, /feedmenu |
| ---            | Bypass heal cooldown | healandfeed.bypass.heal       | OP       | ---             | ---                        |
| ---            | Bypass feed cooldown | healandfeed.bypass.feed       | OP       | ---             | ---                        |

# Cooldown
Cooldown is set in `config.yml` in seconds. Set to `0` to disable it.
```yaml
cooldown:
  heal: 0
  feed: 0
```
Cooldown applies to `/heal`, `/heal <player>`, `/feed`, `/feed <player>` and the GUI buttons. It does not apply to `/healall`, `/feedall` and console.