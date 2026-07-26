package org.kylan1940.healandfeed.command;

import org.bukkit.Bukkit;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
import org.kylan1940.healandfeed.message.MessageUtil;

public class HealAllCommand implements CommandExecutor {

    @Override
    public boolean onCommand(@NotNull CommandSender sender,
                             @NotNull Command command,
                             @NotNull String label,
                             @NotNull String[] args) {

        if (!sender.hasPermission("healandfeed-healall.command")) {
            MessageUtil.send(sender, "no-permission.healall");
            return true;
        }

        if (Bukkit.getOnlinePlayers().isEmpty()) {
            MessageUtil.send(sender, "no-player.online");
            return true;
        }

        for (Player player : Bukkit.getOnlinePlayers()) {
            heal(player);
            MessageUtil.send(player, "messages.heal");
        }

        MessageUtil.send(sender, "messages.heal-all");
        return true;
    }


    private void heal(Player player) {
        double maxHealth = player.getAttribute(org.bukkit.attribute.Attribute.MAX_HEALTH).getValue();
        player.setHealth(maxHealth);
    }
}