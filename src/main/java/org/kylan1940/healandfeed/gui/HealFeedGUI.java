package org.kylan1940.healandfeed.gui;

import org.bukkit.Bukkit;
import org.bukkit.Material;
import org.bukkit.entity.Player;
import org.bukkit.inventory.Inventory;
import org.bukkit.inventory.ItemStack;
import org.bukkit.inventory.meta.ItemMeta;

import java.util.Arrays;

public final class HealFeedGUI {

    public static final String TITLE = "§8HealAndFeed";

    private HealFeedGUI() {
    }

    public static void open(Player player) {

        Inventory inventory = Bukkit.createInventory(null, 54, TITLE);

        ItemStack glass = item(Material.GRAY_STAINED_GLASS_PANE, " ");

        for (int i = 0; i < inventory.getSize(); i++) {
            inventory.setItem(i, glass);
        }

        /*
         * HEAL SIDE
         *
         * Columns 0-3
         */

        inventory.setItem(
                10,
                item(
                        Material.RED_DYE,
                        "§cHeal",
                        "§7Restore your health"
                )
        );

        inventory.setItem(
                19,
                item(
                        Material.PLAYER_HEAD,
                        "§cHeal Player",
                        "§7Heal another online player"
                )
        );

        inventory.setItem(
                28,
                item(
                        Material.GOLDEN_APPLE,
                        "§cHeal All",
                        "§7Heal all online players"
                )
        );

        /*
         * FEED SIDE
         *
         * Columns 5-8
         */

        inventory.setItem(
                16,
                item(
                        Material.COOKED_BEEF,
                        "§6Feed",
                        "§7Restore your hunger"
                )
        );

        inventory.setItem(
                25,
                item(
                        Material.PLAYER_HEAD,
                        "§6Feed Player",
                        "§7Feed another online player"
                )
        );

        inventory.setItem(
                34,
                item(
                        Material.GOLDEN_CARROT,
                        "§6Feed All",
                        "§7Feed all online players"
                )
        );

        // CLOSE

        inventory.setItem(
                49,
                item(Material.BARRIER, "§cClose")
        );

        player.openInventory(inventory);
    }

    private static ItemStack item(
            Material material,
            String name,
            String... lore
    ) {

        ItemStack item = new ItemStack(material);
        ItemMeta meta = item.getItemMeta();

        if (meta != null) {
            meta.setDisplayName(name);
            meta.setLore(Arrays.asList(lore));
            item.setItemMeta(meta);
        }

        return item;
    }
}