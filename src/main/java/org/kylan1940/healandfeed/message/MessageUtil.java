package org.kylan1940.healandfeed.message;

import org.bukkit.ChatColor;
import org.bukkit.command.CommandSender;
import org.kylan1940.healandfeed.HealAndFeed;

import java.util.List;

public final class MessageUtil {

    private MessageUtil() {
    }

    public static void send(CommandSender sender, String path, String... placeholders) {

        List<String> messages = HealAndFeed.getInstance().getConfig().getStringList(path);

        if (messages.isEmpty()) {
            String message = HealAndFeed.getInstance().getConfig().getString(path);

            if (message == null || message.isEmpty()) {
                return;
            }

            sender.sendMessage(color(replace(message, placeholders)));
            return;
        }

        for (String message : messages) {
            sender.sendMessage(color(replace(message, placeholders)));
        }
    }

    private static String replace(String message, String... placeholders) {

        String prefix = HealAndFeed.getInstance().getConfig().getString("prefix", "");

        message = message.replace("%prefix%", prefix);

        for (int i = 0; i < placeholders.length; i += 2) {
            message = message.replace(placeholders[i], placeholders[i + 1]);
        }

        return message;
    }

    private static String color(String message) {
        return ChatColor.translateAlternateColorCodes('&', message);
    }

}