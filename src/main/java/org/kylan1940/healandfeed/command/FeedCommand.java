package org.kylan1940.healandfeed.command;

import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
import org.kylan1940.healandfeed.message.MessageUtil;

public class FeedCommand implements CommandExecutor {

    @Override
    public boolean onCommand(
            @NotNull CommandSender sender,
            @NotNull Command command,
            @NotNull String label,
            @NotNull String[] args
    ) {

        if (args.length == 0) {

            if (!(sender instanceof Player)) {MessageUtil.send(sender, "console-command.feed");
                return true;
            }

            Player player = (Player) sender;

            if (!player.hasPermission("healandfeed-feed.command")) {
                MessageUtil.send(player, "no-permission.feed");
                return true;
            }

            execute(player);
            return true;
        }

        if (!sender.hasPermission("healandfeed-feedother.command")) {
            MessageUtil.send(sender, "no-permission.feedother");
            return true;
        }

        Player target = sender.getServer().getPlayerExact(args[0]);

        if (target == null) {
            MessageUtil.send(sender, "no-player.found");
            return true;
        }

        execute(sender, target);
        return true;
    }

    public void execute(Player player) {
        feed(player);
        MessageUtil.send(player, "messages.fed");
    }

    public void execute(
            CommandSender sender,
            Player target
    ) {

        feed(target);
        MessageUtil.send(target, "messages.fed");

        if (!target.equals(sender)) {
            MessageUtil.send(sender, "messages.feed-other", "%player%", target.getName());
        }
    }

    private void feed(Player player) {
        player.setFoodLevel(20);
        player.setSaturation(20.0F);
    }
}