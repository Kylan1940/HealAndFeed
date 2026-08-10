package org.kylan1940.healandfeed;

import org.bukkit.plugin.java.JavaPlugin;
import org.kylan1940.healandfeed.command.FeedAllCommand;
import org.kylan1940.healandfeed.command.FeedCommand;
import org.kylan1940.healandfeed.command.HealAllCommand;
import org.kylan1940.healandfeed.command.HealCommand;
import org.kylan1940.healandfeed.command.HealFeedCommand;
import org.kylan1940.healandfeed.gui.HealFeedGUIListener;
import org.kylan1940.healandfeed.utils.ConfigUpdater;

public final class HealAndFeed extends JavaPlugin {

    private static HealAndFeed instance;

    private HealCommand healCommand;
    private FeedCommand feedCommand;
    private HealAllCommand healAllCommand;
    private FeedAllCommand feedAllCommand;

    @Override
    public void onEnable() {

        instance = this;

        saveDefaultConfig();
        ConfigUpdater.update(this);

        healCommand = new HealCommand();
        feedCommand = new FeedCommand();
        healAllCommand = new HealAllCommand();
        feedAllCommand = new FeedAllCommand();

        getCommand("heal").setExecutor(healCommand);
        getCommand("feed").setExecutor(feedCommand);
        getCommand("healall").setExecutor(healAllCommand);
        getCommand("feedall").setExecutor(feedAllCommand);
        getCommand("healfeed").setExecutor(new HealFeedCommand());

        getServer().getPluginManager().registerEvents(new HealFeedGUIListener(healCommand, feedCommand, healAllCommand, feedAllCommand), this);

        getLogger().info("HealAndFeed enabled.");
    }

    @Override
    public void onDisable() {
    }

    public static HealAndFeed getInstance() {
        return instance;
    }
}