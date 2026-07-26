package org.kylan1940.healandfeed.command;

import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
import org.kylan1940.healandfeed.message.MessageUtil;

public class HealCommand implements CommandExecutor {

    @Override
    public boolean onCommand(@NotNull CommandSender sender,
                             @NotNull Command command,
                             @NotNull String label,
                             @NotNull String[] args) {

        if (args.length == 0) {

            if (!(sender instanceof Player player)) {
                MessageUtil.send(sender, "console-command.heal");
                return true;
            }

            if (!player.hasPermission("healandfeed-heal.command")) {
                MessageUtil.send(player, "no-permission.heal");
                return true;
            }

            heal(player);

            MessageUtil.send(player, "messages.heal");
            return true;
        }

        if (!sender.hasPermission("healandfeed-healother.command")) {
            MessageUtil.send(sender, "no-permission.healother");
            return true;
        }

        Player target = sender.getServer().getPlayerExact(args[0]);

        if (target == null) {
            MessageUtil.send(sender, "no-player.found");
            return true;
        }

        heal(target);
        MessageUtil.send(target, "messages.heal");

        if (!target.equals(sender)) {
            MessageUtil.send(sender, "messages.heal-other", "%player%", target.getName());
        }

        return true;
    }

    private void heal(Player player) {
        double maxHealth = player.getAttribute(org.bukkit.attribute.Attribute.MAX_HEALTH).getValue();
        player.setHealth(maxHealth);
    }
}