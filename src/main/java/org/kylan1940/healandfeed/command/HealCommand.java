package org.kylan1940.healandfeed.command;

import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
import org.kylan1940.healandfeed.HealAndFeed;
import org.kylan1940.healandfeed.message.MessageUtil;
import org.kylan1940.healandfeed.utils.CooldownManager;

public class HealCommand implements CommandExecutor {

    @Override
    public boolean onCommand(
            @NotNull CommandSender sender,
            @NotNull Command command,
            @NotNull String label,
            @NotNull String[] args
    ) {

        if (args.length == 0) {

            if (!(sender instanceof Player)) {
                MessageUtil.send(sender, "console-command.heal");
                return true;
            }

            Player player = (Player) sender;

            if (!player.hasPermission("healandfeed-heal.command")) {
                MessageUtil.send(player, "no-permission.heal");
                return true;
            }

            execute(player);
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

        execute(sender, target);
        return true;
    }

    public void execute(Player player) {

        if (isOnCooldown(player)) {
            return;
        }

        heal(player);
        MessageUtil.send(player, "messages.heal");
        startCooldown(player);
    }

    public void execute(
            CommandSender sender,
            Player target
    ) {

        if (sender instanceof Player && isOnCooldown((Player) sender)) {
            return;
        }

        heal(target);
        MessageUtil.send(target, "messages.heal");

        if (!target.equals(sender)) {
            MessageUtil.send(sender, "messages.heal-other", "%player%", target.getName());
        }

        if (sender instanceof Player) {
            startCooldown((Player) sender);
        }
    }

    private long getCooldownMillis() {
        return Math.max(HealAndFeed.getInstance().getConfig().getLong("cooldown.heal", 0), 0) * 1000L;
    }

    private boolean isOnCooldown(Player player) {

        long cooldown = getCooldownMillis();

        if (cooldown <= 0 || player.hasPermission("healandfeed.bypass.heal")) {
            return false;
        }

        if (!CooldownManager.isHealOnCooldown(player.getUniqueId(), cooldown)) {
            return false;
        }

        long remainingSeconds = (long) Math.ceil(CooldownManager.getHealRemaining(player.getUniqueId(), cooldown) / 1000.0);
        MessageUtil.send(player, "messages.cooldown.heal", "%time%", String.valueOf(remainingSeconds));
        return true;
    }

    private void startCooldown(Player player) {

        if (getCooldownMillis() <= 0 || player.hasPermission("healandfeed.bypass.heal")) {
            return;
        }

        CooldownManager.setHealCooldown(player.getUniqueId());
    }

    private void heal(Player player) {

        if (player.getAttribute(org.bukkit.attribute.Attribute.MAX_HEALTH) == null) {
            return;
        }

        double maxHealth = player.getAttribute(org.bukkit.attribute.Attribute.MAX_HEALTH).getValue();
        player.setHealth(maxHealth);
    }
}