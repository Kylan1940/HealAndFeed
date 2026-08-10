package org.kylan1940.healandfeed.gui;

import org.bukkit.Bukkit;
import org.bukkit.Material;
import org.bukkit.entity.Player;
import org.bukkit.inventory.Inventory;
import org.bukkit.inventory.ItemStack;
import org.bukkit.inventory.meta.ItemMeta;
import org.bukkit.inventory.meta.SkullMeta;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public final class PlayerSelectGUI {

    public static final String HEAL_TITLE = "§8Select Player - Heal";
    public static final String FEED_TITLE = "§8Select Player - Feed";

    private static final int SIZE = 54;
    private static final int PLAYERS_PER_PAGE = 45;

    private PlayerSelectGUI() {
    }

    public enum Action {
        HEAL,
        FEED
    }

    public static void open(
            Player viewer,
            Action action,
            int page
    ) {

        List<Player> players = new ArrayList<>(Bukkit.getOnlinePlayers());

        int maxPage = Math.max(0, (players.size() - 1) / PLAYERS_PER_PAGE);

        if (page < 0) {
            page = 0;
        }

        if (page > maxPage) {
            page = maxPage;
        }

        String title = action == Action.HEAL ? HEAL_TITLE : FEED_TITLE;

        Inventory inventory = Bukkit.createInventory(null, SIZE, title);

        ItemStack glass = item(
                Material.GRAY_STAINED_GLASS_PANE,
                " "
        );

        for (int i = 0; i < SIZE; i++) {
            inventory.setItem(i, glass);
        }

        int start = page * PLAYERS_PER_PAGE;
        int end = Math.min(
                start + PLAYERS_PER_PAGE,
                players.size()
        );

        for (int i = start; i < end; i++) {

            Player target = players.get(i);

            inventory.setItem(
                    i - start,
                    playerHead(
                            target,
                            action
                    )
            );
        }

        // Previous
        if (page > 0) {
            inventory.setItem(
                    48,
                    item(
                            Material.ARROW,
                            "§ePrevious Page"
                    )
            );
        }

        // Back
        inventory.setItem(
                49,
                item(
                        Material.BARRIER,
                        "§cBack"
                )
        );

        // Next
        if (page < maxPage) {
            inventory.setItem(
                    50,
                    item(
                            Material.ARROW,
                            "§eNext Page"
                    )
            );
        }

        viewer.openInventory(inventory);
    }

    private static ItemStack playerHead(
            Player player,
            Action action
    ) {

        ItemStack item = new ItemStack(Material.PLAYER_HEAD);

        SkullMeta meta = (SkullMeta) item.getItemMeta();

        if (meta != null) {

            meta.setOwningPlayer(player);

            meta.setDisplayName("§e" + player.getName());

            meta.setLore(Arrays.asList(action == Action.HEAL
                    ? "§7Click to heal this player"
                    : "§7Click to feed this player"
            ));

            item.setItemMeta(meta);
        }

        return item;
    }

    private static ItemStack item(
            Material material,
            String name
    ) {

        ItemStack item = new ItemStack(material);
        ItemMeta meta = item.getItemMeta();

        if (meta != null) {
            meta.setDisplayName(name);
            item.setItemMeta(meta);
        }

        return item;
    }
}