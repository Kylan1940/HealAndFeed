package org.kylan1940.healandfeed.gui;

import org.bukkit.Bukkit;
import org.bukkit.entity.Player;
import org.bukkit.event.EventHandler;
import org.bukkit.event.Listener;
import org.bukkit.event.inventory.InventoryClickEvent;
import org.bukkit.inventory.ItemStack;
import org.bukkit.inventory.meta.SkullMeta;
import org.kylan1940.healandfeed.command.FeedAllCommand;
import org.kylan1940.healandfeed.command.FeedCommand;
import org.kylan1940.healandfeed.command.HealAllCommand;
import org.kylan1940.healandfeed.command.HealCommand;
import org.kylan1940.healandfeed.message.MessageUtil;

public class HealFeedGUIListener implements Listener {

    private final HealCommand healCommand;
    private final FeedCommand feedCommand;
    private final HealAllCommand healAllCommand;
    private final FeedAllCommand feedAllCommand;

    public HealFeedGUIListener(
            HealCommand healCommand,
            FeedCommand feedCommand,
            HealAllCommand healAllCommand,
            FeedAllCommand feedAllCommand
    ) {

        this.healCommand = healCommand;
        this.feedCommand = feedCommand;
        this.healAllCommand = healAllCommand;
        this.feedAllCommand = feedAllCommand;
    }

    @EventHandler
    public void onInventoryClick(
            InventoryClickEvent event
    ) {

        if (!(event.getWhoClicked() instanceof Player)) {
            return;
        }

        Player player = (Player) event.getWhoClicked();

        String title = event.getView().getTitle();

        if (title.equals(HealFeedGUI.TITLE)) {

            event.setCancelled(true);

            if (event.getRawSlot() >= event.getView().getTopInventory().getSize()) {
                return;
            }

            handleMainGUI(player, event.getRawSlot());
            return;
        }

        if (title.equals(PlayerSelectGUI.HEAL_TITLE)) {

            event.setCancelled(true);

            if (event.getRawSlot() >= 45) {
                handleNavigation(
                        player,
                        event.getRawSlot(),
                        PlayerSelectGUI.Action.HEAL,
                        title
                );

                return;
            }

            handlePlayer(
                    player,
                    event,
                    PlayerSelectGUI.Action.HEAL
            );

            return;
        }

        if (title.equals(PlayerSelectGUI.FEED_TITLE)) {

            event.setCancelled(true);

            if (event.getRawSlot() >= 45) {
                handleNavigation(
                        player,
                        event.getRawSlot(),
                        PlayerSelectGUI.Action.FEED,
                        title
                );

                return;
            }

            handlePlayer(
                    player,
                    event,
                    PlayerSelectGUI.Action.FEED
            );
        }
    }

    private void handleMainGUI(
            Player player,
            int slot
    ) {

        switch (slot) {

            case 10:

                if (!player.hasPermission("healandfeed-heal.command")) {
                    MessageUtil.send(player, "no-permission.heal");
                    return;
                }

                healCommand.execute(player);
                break;

            case 19:

                if (!player.hasPermission("healandfeed-healother.command")) {
                    MessageUtil.send(player, "no-permission.healother");
                    return;
                }

                PlayerSelectGUI.open(player, PlayerSelectGUI.Action.HEAL, 0);
                break;

            case 28:

                if (!player.hasPermission("healandfeed-healall.command")) {
                    MessageUtil.send(player, "no-permission.healall");
                    return;
                }

                healAllCommand.execute(player);
                break;

            case 16:

                if (!player.hasPermission("healandfeed-feed.command")) {
                    MessageUtil.send(player, "no-permission.feed");
                    return;
                }

                feedCommand.execute(player);
                break;

            case 25:

                if (!player.hasPermission("healandfeed-feedother.command")) {
                    MessageUtil.send(player, "no-permission.feedother");
                    return;
                }

                PlayerSelectGUI.open(player, PlayerSelectGUI.Action.FEED, 0);
                break;

            case 34:

                if (!player.hasPermission("healandfeed-feedall.command")) {
                    MessageUtil.send(player, "no-permission.feedall");
                    return;
                }

                feedAllCommand.execute(player);
                break;

            case 49:
                player.closeInventory();
                break;

        }
    }

    private void handlePlayer(
            Player viewer,
            InventoryClickEvent event,
            PlayerSelectGUI.Action action
    ) {

        ItemStack item =
                event.getCurrentItem();

        if (item == null) {
            return;
        }

        if (!(item.getItemMeta() instanceof SkullMeta)) {
            return;
        }

        SkullMeta meta = (SkullMeta) item.getItemMeta();

        Player target = meta.getOwningPlayer() != null ? Bukkit.getPlayer(meta.getOwningPlayer().getUniqueId()) : null;

        if (target == null) {
            MessageUtil.send(viewer, "no-player.found");
            return;
        }

        if (action == PlayerSelectGUI.Action.HEAL) {

            if (!viewer.hasPermission("healandfeed-healother.command")) {
                MessageUtil.send(viewer, "no-permission.healother");
                return;
            }

            healCommand.execute(viewer, target);

        } else {

            if (!viewer.hasPermission("healandfeed-feedother.command")) {
                MessageUtil.send(viewer, "no-permission.feedother");
                return;
            }

            feedCommand.execute(viewer, target);
        }

        viewer.closeInventory();
    }

    private void handleNavigation(
            Player player,
            int slot,
            PlayerSelectGUI.Action action,
            String title
    ) {

        int page = getCurrentPage(title);

        if (slot == 49) {
            HealFeedGUI.open(player);
            return;
        }

        if (slot == 48 && page > 0) {
            PlayerSelectGUI.open(player, action, page - 1);
            return;
        }

        if (slot == 50) {
            PlayerSelectGUI.open(player, action, page + 1);
        }
    }

    private int getCurrentPage(String title) {
        return 0;
    }
}