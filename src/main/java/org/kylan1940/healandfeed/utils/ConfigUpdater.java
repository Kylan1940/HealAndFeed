package org.kylan1940.healandfeed.utils;

import org.bukkit.configuration.file.FileConfiguration;
import org.bukkit.plugin.java.JavaPlugin;

import java.io.File;

public class ConfigUpdater {

    private static final int CONFIG_VERSION = 2;

    public static void update(JavaPlugin plugin) {

        File configFile = new File(plugin.getDataFolder(), "config.yml");

        if (!configFile.exists()) {
            plugin.saveResource("config.yml", false);
            return;
        }

        FileConfiguration config = plugin.getConfig();

        int currentVersion = config.getInt("config-version", 1);

        if (currentVersion == CONFIG_VERSION) {
            return;
        }

        plugin.getLogger().info("Your config isn't the latest. " + "Renaming old config to config-" + currentVersion + ".yml");

        File backup = new File(plugin.getDataFolder(), "config-" + currentVersion + ".yml");

        if (!configFile.renameTo(backup)) {
            plugin.getLogger().warning("Failed to backup old config.yml!");
            return;
        }

        plugin.saveResource("config.yml", false);

        plugin.reloadConfig();

        plugin.getLogger().info("Config updated to version " + CONFIG_VERSION);
    }
}