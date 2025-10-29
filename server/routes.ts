import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";
import { z } from "zod";
import { insertAppSchema, insertAdmobAccountSchema, insertSwitchingRuleSchema, insertNotificationSchema, insertDeviceSchema, insertAnalyticsEventSchema, insertNotificationEventSchema } from "@shared/schema";
import bcrypt from "bcrypt";
import { authMiddleware, generateToken, type AuthRequest } from "./middleware/auth";

// Helper function for async route handlers
const asyncHandler = (fn: Function) => (req: any, res: any, next: any) => {
  Promise.resolve(fn(req, res, next)).catch(next);
};

export async function registerRoutes(app: Express): Promise<Server> {
  // ============================================
  // AUTHENTICATION ROUTES
  // ============================================

  // Login endpoint
  app.post("/api/auth/login", asyncHandler(async (req: any, res: any) => {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({ message: "Username and password required" });
    }

    // Get user from database
    let user = await storage.getAdminUserByUsername(username);

    // Create default admin user if it doesn't exist (for initial setup)
    if (!user && username === "admin") {
      const hashedPassword = await bcrypt.hash("admin123", 10);
      user = await storage.createAdminUser({
        username: "admin",
        password: hashedPassword,
        role: "super_admin",
      });
    }

    if (!user) {
      return res.status(401).json({ message: "Invalid credentials" });
    }

    // Verify password
    const isValid = await bcrypt.compare(password, user.password);
    if (!isValid) {
      return res.status(401).json({ message: "Invalid credentials" });
    }

    // Generate JWT token
    const token = generateToken({
      id: user.id,
      username: user.username,
      role: user.role,
    });

    // Return user data (without password) and token
    const { password: _, ...userWithoutPassword } = user;
    res.json({
      user: userWithoutPassword,
      token,
    });
  }));

  // ============================================
  // ADMIN ROUTES - APPS
  // ============================================

  // Apply authentication middleware to all admin routes
  app.use("/api/admin/*", authMiddleware);

  app.get("/api/admin/apps", asyncHandler(async (req: any, res: any) => {
    const apps = await storage.getAllApps();
    res.json(apps);
  }));

  app.get("/api/admin/apps/:id", asyncHandler(async (req: any, res: any) => {
    const app = await storage.getApp(req.params.id);
    if (!app) {
      return res.status(404).json({ message: "App not found" });
    }
    res.json(app);
  }));

  app.post("/api/admin/apps", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertAppSchema.parse(req.body);
    
    // Check if package name already exists
    const existing = await storage.getAppByPackageName(validatedData.packageName);
    if (existing) {
      return res.status(400).json({ message: "Package name already exists" });
    }

    const app = await storage.createApp(validatedData);
    res.status(201).json(app);
  }));

  app.put("/api/admin/apps/:id", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertAppSchema.partial().parse(req.body);
    const app = await storage.updateApp(req.params.id, validatedData);
    
    if (!app) {
      return res.status(404).json({ message: "App not found" });
    }
    
    res.json(app);
  }));

  app.delete("/api/admin/apps/:id", asyncHandler(async (req: any, res: any) => {
    await storage.deleteApp(req.params.id);
    res.status(204).send();
  }));

  // ============================================
  // ADMIN ROUTES - ADMOB ACCOUNTS
  // ============================================

  app.get("/api/admin/admob-accounts", asyncHandler(async (req: any, res: any) => {
    const accounts = await storage.getAllAdmobAccounts();
    res.json(accounts);
  }));

  app.get("/api/admin/admob-accounts/:id", asyncHandler(async (req: any, res: any) => {
    const account = await storage.getAdmobAccount(req.params.id);
    if (!account) {
      return res.status(404).json({ message: "Account not found" });
    }
    res.json(account);
  }));

  app.post("/api/admin/admob-accounts", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertAdmobAccountSchema.parse(req.body);
    const account = await storage.createAdmobAccount(validatedData);
    res.status(201).json(account);
  }));

  app.put("/api/admin/admob-accounts/:id", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertAdmobAccountSchema.partial().parse(req.body);
    const account = await storage.updateAdmobAccount(req.params.id, validatedData);
    
    if (!account) {
      return res.status(404).json({ message: "Account not found" });
    }
    
    res.json(account);
  }));

  app.delete("/api/admin/admob-accounts/:id", asyncHandler(async (req: any, res: any) => {
    await storage.deleteAdmobAccount(req.params.id);
    res.status(204).send();
  }));

  // ============================================
  // ADMIN ROUTES - SWITCHING RULES
  // ============================================

  app.get("/api/admin/switching-rules", asyncHandler(async (req: any, res: any) => {
    const rules = await storage.getAllSwitchingRules();
    res.json(rules);
  }));

  app.get("/api/admin/switching-rules/:id", asyncHandler(async (req: any, res: any) => {
    const rule = await storage.getSwitchingRule(req.params.id);
    if (!rule) {
      return res.status(404).json({ message: "Rule not found" });
    }
    res.json(rule);
  }));

  app.post("/api/admin/switching-rules", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertSwitchingRuleSchema.parse(req.body);
    const rule = await storage.createSwitchingRule(validatedData);
    res.status(201).json(rule);
  }));

  app.put("/api/admin/switching-rules/:id", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertSwitchingRuleSchema.partial().parse(req.body);
    const rule = await storage.updateSwitchingRule(req.params.id, validatedData);
    
    if (!rule) {
      return res.status(404).json({ message: "Rule not found" });
    }
    
    res.json(rule);
  }));

  app.delete("/api/admin/switching-rules/:id", asyncHandler(async (req: any, res: any) => {
    await storage.deleteSwitchingRule(req.params.id);
    res.status(204).send();
  }));

  // ============================================
  // ADMIN ROUTES - NOTIFICATIONS
  // ============================================

  app.get("/api/admin/notifications", asyncHandler(async (req: any, res: any) => {
    const notifications = await storage.getAllNotifications();
    res.json(notifications);
  }));

  app.get("/api/admin/notifications/:id", asyncHandler(async (req: any, res: any) => {
    const notification = await storage.getNotification(req.params.id);
    if (!notification) {
      return res.status(404).json({ message: "Notification not found" });
    }
    res.json(notification);
  }));

  app.post("/api/admin/notifications", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertNotificationSchema.parse(req.body);
    const notification = await storage.createNotification(validatedData);
    res.status(201).json(notification);
  }));

  app.put("/api/admin/notifications/:id", asyncHandler(async (req: any, res: any) => {
    const validatedData = insertNotificationSchema.partial().parse(req.body);
    const notification = await storage.updateNotification(req.params.id, validatedData);
    
    if (!notification) {
      return res.status(404).json({ message: "Notification not found" });
    }
    
    res.json(notification);
  }));

  app.delete("/api/admin/notifications/:id", asyncHandler(async (req: any, res: any) => {
    await storage.deleteNotification(req.params.id);
    res.status(204).send();
  }));

  app.post("/api/admin/notifications/:id/send", asyncHandler(async (req: any, res: any) => {
    const notification = await storage.updateNotification(req.params.id, {
      status: "sent",
      startDate: new Date(),
    });
    
    if (!notification) {
      return res.status(404).json({ message: "Notification not found" });
    }
    
    res.json(notification);
  }));

  // ============================================
  // ADMIN ROUTES - DASHBOARD
  // ============================================

  app.get("/api/admin/dashboard/stats", asyncHandler(async (req: any, res: any) => {
    const stats = await storage.getDashboardStats();
    res.json(stats);
  }));

  // ============================================
  // PUBLIC API ROUTES - FOR ANDROID APPS
  // ============================================

  // Get app configuration (AdMob accounts and switching rules)
  app.get("/api/v1/config/:packageName", asyncHandler(async (req: any, res: any) => {
    const app = await storage.getAppByPackageName(req.params.packageName);
    if (!app) {
      return res.status(404).json({ message: "App not found" });
    }

    const admobAccounts = await storage.getAdmobAccountsByApp(app.id);
    const switchingRule = await storage.getSwitchingRuleByApp(app.id);
    const notifications = await storage.getNotificationsByApp(app.id);

    res.json({
      app_id: app.packageName,
      version: "1.0.0",
      last_update: new Date().toISOString(),
      admob_accounts: admobAccounts.map(account => ({
        account_id: account.id,
        account_name: account.accountName,
        status: account.status,
        priority: account.priority,
        weight: account.weight,
        banner_id: account.bannerId,
        interstitial_id: account.interstitialId,
        rewarded_id: account.rewardedId,
        app_open_id: account.appOpenId,
        native_id: account.nativeId,
      })),
      switching_rules: switchingRule ? {
        strategy: switchingRule.strategy,
        rotation_interval: switchingRule.rotationInterval,
        fallback_enabled: switchingRule.fallbackEnabled,
        ab_testing_enabled: switchingRule.abTestingEnabled,
        geographic_rules: switchingRule.geographicRules,
      } : {
        strategy: "weighted_random",
        rotation_interval: "daily",
        fallback_enabled: true,
        ab_testing_enabled: false,
      },
      notifications: notifications
        .filter(n => n.status === "sent" || n.status === "scheduled")
        .map(notif => ({
          notification_id: notif.id,
          title: notif.title,
          message: notif.message,
          type: notif.type,
          priority: notif.priority,
          targeting: {
            countries: notif.targetCountries,
            app_versions: notif.targetAppVersions,
            min_android_version: notif.minAndroidVersion,
            user_segments: notif.userSegments,
          },
          schedule: {
            start_date: notif.startDate,
            end_date: notif.endDate,
            recurring: notif.recurring,
            frequency: notif.frequency,
          },
          content: {
            image_url: notif.imageUrl,
            action_button_text: notif.actionButtonText,
            action_type: notif.actionType,
            action_value: notif.actionValue,
            cancelable: notif.cancelable,
          },
          display_rules: {
            max_displays: notif.maxDisplays,
            display_interval_hours: notif.displayIntervalHours,
            show_on_app_launch: notif.showOnAppLaunch,
          },
        })),
    });
  }));

  // Track analytics events
  app.post("/api/v1/analytics/admob", asyncHandler(async (req: any, res: any) => {
    const schema = z.object({
      package_name: z.string(),
      account_id: z.string().optional(),
      event: z.string(),
      ad_type: z.string().optional(),
      value: z.number().optional(),
      timestamp: z.string().optional(),
    });

    const data = schema.parse(req.body);
    const app = await storage.getAppByPackageName(data.package_name);
    
    if (!app) {
      return res.status(404).json({ message: "App not found" });
    }

    await storage.createAnalyticsEvent({
      appId: app.id,
      accountId: data.account_id,
      eventType: data.event,
      adType: data.ad_type,
      value: data.value,
    });

    res.status(201).json({ message: "Event tracked successfully" });
  }));

  // Register device for push notifications
  app.post("/api/v1/device/register", asyncHandler(async (req: any, res: any) => {
    const schema = z.object({
      package_name: z.string(),
      fcm_token: z.string(),
      device_info: z.object({
        country: z.string().optional(),
        app_version: z.string().optional(),
        android_version: z.number().optional(),
        manufacturer: z.string().optional(),
        model: z.string().optional(),
      }).optional(),
    });

    const data = schema.parse(req.body);
    const app = await storage.getAppByPackageName(data.package_name);
    
    if (!app) {
      return res.status(404).json({ message: "App not found" });
    }

    const device = await storage.createDevice({
      appId: app.id,
      fcmToken: data.fcm_token,
      country: data.device_info?.country,
      appVersion: data.device_info?.app_version,
      androidVersion: data.device_info?.android_version,
      deviceManufacturer: data.device_info?.manufacturer,
      deviceModel: data.device_info?.model,
    });

    res.status(201).json({ message: "Device registered successfully", device_id: device.id });
  }));

  // Get pending notifications for a device
  app.get("/api/v1/notifications/pending", asyncHandler(async (req: any, res: any) => {
    const packageName = req.query.package_name as string;
    
    if (!packageName) {
      return res.status(400).json({ message: "package_name required" });
    }

    const app = await storage.getAppByPackageName(packageName);
    if (!app) {
      return res.status(404).json({ message: "App not found" });
    }

    const notifications = await storage.getNotificationsByApp(app.id);
    const pending = notifications.filter(n => n.status === "sent" || n.status === "scheduled");

    res.json({ notifications: pending });
  }));

  // Track notification events
  app.post("/api/v1/notifications/track", asyncHandler(async (req: any, res: any) => {
    const schema = z.object({
      notification_id: z.string(),
      device_id: z.string().optional(),
      event: z.string(),
      timestamp: z.string().optional(),
    });

    const data = schema.parse(req.body);

    await storage.createNotificationEvent({
      notificationId: data.notification_id,
      deviceId: data.device_id,
      eventType: data.event,
    });

    res.status(201).json({ message: "Event tracked successfully" });
  }));

  const httpServer = createServer(app);
  return httpServer;
}
