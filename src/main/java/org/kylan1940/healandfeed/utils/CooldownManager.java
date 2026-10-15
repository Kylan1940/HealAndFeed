package org.kylan1940.healandfeed.utils;

import java.util.HashMap;
import java.util.Map;
import java.util.UUID;

public final class CooldownManager {

    private static final Map<UUID, Long> HEAL_COOLDOWNS = new HashMap<>();
    private static final Map<UUID, Long> FEED_COOLDOWNS = new HashMap<>();

    private CooldownManager() {}

    public static boolean isHealOnCooldown(UUID uuid, long cooldown) {
        Long lastUse = HEAL_COOLDOWNS.get(uuid);
        if (lastUse == null) {
            return false;
        }
        long remaining = (lastUse + cooldown) - System.currentTimeMillis();
        if (remaining <= 0) {
            HEAL_COOLDOWNS.remove(uuid);
            return false;
        }
        return true;
    }

    public static boolean isFeedOnCooldown(UUID uuid, long cooldown) {
        Long lastUse = FEED_COOLDOWNS.get(uuid);
        if (lastUse == null) {
            return false;
        }
        long remaining = (lastUse + cooldown) - System.currentTimeMillis();
        if (remaining <= 0) {
            FEED_COOLDOWNS.remove(uuid);
            return false;
        }
        return true;
    }

    public static long getHealRemaining(UUID uuid, long cooldown) {
        Long lastUse = HEAL_COOLDOWNS.get(uuid);
        if (lastUse == null) {
            return 0;
        }
        return Math.max((lastUse + cooldown) - System.currentTimeMillis(), 0);
    }

    public static long getFeedRemaining(UUID uuid, long cooldown) {
        Long lastUse = FEED_COOLDOWNS.get(uuid);
        if (lastUse == null) {
            return 0;
        }
        return Math.max((lastUse + cooldown) - System.currentTimeMillis(), 0);
    }

    public static void setHealCooldown(UUID uuid) {
        HEAL_COOLDOWNS.put(uuid, System.currentTimeMillis());
    }

    public static void setFeedCooldown(UUID uuid) {
        FEED_COOLDOWNS.put(uuid, System.currentTimeMillis());
    }

    public static void clear(UUID uuid) {
        HEAL_COOLDOWNS.remove(uuid);
        FEED_COOLDOWNS.remove(uuid);
    }
}