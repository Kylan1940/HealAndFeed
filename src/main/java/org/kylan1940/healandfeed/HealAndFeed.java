package org.kylan1940.healandfeed;

import org.bukkit.plugin.java.JavaPlugin;
import org.kylan1940.healandfeed.command.FeedAllCommand;
import org.kylan1940.healandfeed.command.FeedCommand;
import org.kylan1940.healandfeed.command.HealAllCommand;
import org.kylan1940.healandfeed.command.HealCommand;
import org.kylan1940.healandfeed.utils.ConfigUpdater;

public final class HealAndFeed extends JavaPlugin {

    private static HealAndFeed instance;

    @Override
    public void onEnable() {

        instance = this;

        saveDefaultConfig();
        ConfigUpdater.update(this);

        getCommand("heal").setExecutor(new HealCommand());

        getCommand("feed").setExecutor(new FeedCommand());

        getCommand("healall").setExecutor(new HealAllCommand());

        getCommand("feedall").setExecutor(new FeedAllCommand());

        getLogger().info("HealAndFeed enabled.");
    }

    @Override
    public void onDisable() {

    }

    public static HealAndFeed getInstance() {
        return instance;
    }

}