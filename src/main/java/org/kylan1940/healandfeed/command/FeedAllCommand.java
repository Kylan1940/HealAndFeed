package org.kylan1940.healandfeed.command;

import org.bukkit.Bukkit;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
import org.kylan1940.healandfeed.message.MessageUtil;

public class FeedAllCommand implements CommandExecutor {

    @Override
    public boolean onCommand(
            @NotNull CommandSender sender,
            @NotNull Command command,
            @NotNull String label,
            @NotNull String[] args
    ) {
        if (!sender.hasPermission("healandfeed-feedall.command")) {
            MessageUtil.send(sender, "no-permission.feedall");
            return true;
        }
        execute(sender);
        return true;
    }

    public void execute(CommandSender sender) {

        if (Bukkit.getOnlinePlayers().isEmpty()) {MessageUtil.send(sender, "no-player.online");
            return;
        }
        for (Player player : Bukkit.getOnlinePlayers()) {
            feed(player);
            MessageUtil.send(player, "messages.fed");
        }

        if (sender instanceof Player) {
            MessageUtil.send(sender, "messages.feed-all");
        } else {
            MessageUtil.send(sender, "console-command.feedall");
        }
    }

    private void feed(Player player) {
        player.setFoodLevel(20);
        player.setSaturation(20.0F);
    }
}