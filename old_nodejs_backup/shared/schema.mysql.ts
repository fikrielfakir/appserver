import { sql } from "drizzle-orm";
import { mysqlTable, varchar, text, timestamp, int, boolean, json } from "drizzle-orm/mysql-core";
import { relations } from "drizzle-orm";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";

// Admin Users
export const adminUsers = mysqlTable("admin_users", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  username: text("username").notNull().unique(),
  password: text("password").notNull(),
  role: varchar("role", { length: 50 }).notNull().default("viewer"), // super_admin, admin, viewer
  createdAt: timestamp("created_at").defaultNow().notNull(),
});

// Android Apps
export const apps = mysqlTable("apps", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  packageName: text("package_name").notNull().unique(),
  appName: text("app_name").notNull(),
  description: text("description"),
  status: varchar("status", { length: 20 }).notNull().default("active"), // active, paused, disabled
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// AdMob Accounts
export const admobAccounts = mysqlTable("admob_accounts", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  appId: varchar("app_id", { length: 36 }).notNull().references(() => apps.id, { onDelete: "cascade" }),
  accountName: text("account_name").notNull(),
  status: varchar("status", { length: 20 }).notNull().default("active"), // active, paused, disabled
  priority: int("priority").notNull().default(1),
  weight: int("weight").notNull().default(50), // percentage for weighted distribution
  bannerId: text("banner_id"),
  interstitialId: text("interstitial_id"),
  rewardedId: text("rewarded_id"),
  appOpenId: text("app_open_id"),
  nativeId: text("native_id"),
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// Switching Rules for Apps
export const switchingRules = mysqlTable("switching_rules", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  appId: varchar("app_id", { length: 36 }).notNull().references(() => apps.id, { onDelete: "cascade" }).unique(),
  strategy: varchar("strategy", { length: 50 }).notNull().default("weighted_random"), // weighted_random, sequential, geographic, time_based
  rotationInterval: varchar("rotation_interval", { length: 20 }).notNull().default("daily"), // hourly, daily, session, random
  fallbackEnabled: boolean("fallback_enabled").notNull().default(true),
  abTestingEnabled: boolean("ab_testing_enabled").notNull().default(false),
  geographicRules: json("geographic_rules"), // Array of {countries: string[], accountId: string}
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// Push Notifications
export const notifications = mysqlTable("notifications", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  appId: varchar("app_id", { length: 36 }).notNull().references(() => apps.id, { onDelete: "cascade" }),
  title: text("title").notNull(),
  message: text("message").notNull(),
  type: varchar("type", { length: 20 }).notNull().default("popup"), // popup, toast, banner, fullscreen
  priority: varchar("priority", { length: 20 }).notNull().default("normal"), // low, normal, high
  status: varchar("status", { length: 20 }).notNull().default("draft"), // draft, scheduled, sent, failed
  
  // Targeting (stored as JSON in MySQL)
  targetCountries: json("target_countries").$type<string[]>(), // Array of country codes
  targetAppVersions: json("target_app_versions").$type<string[]>(),
  minAndroidVersion: int("min_android_version"),
  userSegments: json("user_segments").$type<string[]>(), // new_users, active_users, inactive_users
  
  // Scheduling
  startDate: timestamp("start_date"),
  endDate: timestamp("end_date"),
  recurring: boolean("recurring").notNull().default(false),
  frequency: varchar("frequency", { length: 20 }).notNull().default("once"), // once, daily, weekly
  
  // Content
  imageUrl: text("image_url"),
  actionButtonText: text("action_button_text"),
  actionType: varchar("action_type", { length: 20 }), // deeplink, url, dismiss
  actionValue: text("action_value"),
  cancelable: boolean("cancelable").notNull().default(true),
  
  // Display Rules
  maxDisplays: int("max_displays").notNull().default(1),
  displayIntervalHours: int("display_interval_hours").notNull().default(24),
  showOnAppLaunch: boolean("show_on_app_launch").notNull().default(false),
  
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// Device Registrations
export const devices = mysqlTable("devices", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  appId: varchar("app_id", { length: 36 }).notNull().references(() => apps.id, { onDelete: "cascade" }),
  fcmToken: text("fcm_token").notNull(),
  country: varchar("country", { length: 10 }),
  appVersion: varchar("app_version", { length: 20 }),
  androidVersion: int("android_version"),
  deviceManufacturer: varchar("device_manufacturer", { length: 100 }),
  deviceModel: varchar("device_model", { length: 100 }),
  lastSeen: timestamp("last_seen").defaultNow().notNull(),
  createdAt: timestamp("created_at").defaultNow().notNull(),
});

// Analytics Events
export const analyticsEvents = mysqlTable("analytics_events", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  appId: varchar("app_id", { length: 36 }).notNull().references(() => apps.id, { onDelete: "cascade" }),
  accountId: varchar("account_id", { length: 36 }).references(() => admobAccounts.id, { onDelete: "set null" }),
  eventType: varchar("event_type", { length: 50 }).notNull(), // impression, click, revenue
  adType: varchar("ad_type", { length: 50 }), // banner, interstitial, rewarded, app_open, native
  value: int("value"), // For revenue tracking (in cents)
  country: varchar("country", { length: 10 }),
  timestamp: timestamp("timestamp").defaultNow().notNull(),
});

// Notification Events
export const notificationEvents = mysqlTable("notification_events", {
  id: varchar("id", { length: 36 }).primaryKey().$defaultFn(() => crypto.randomUUID()),
  notificationId: varchar("notification_id", { length: 36 }).notNull().references(() => notifications.id, { onDelete: "cascade" }),
  deviceId: varchar("device_id", { length: 36 }).references(() => devices.id, { onDelete: "set null" }),
  eventType: varchar("event_type", { length: 50 }).notNull(), // displayed, clicked, dismissed
  timestamp: timestamp("timestamp").defaultNow().notNull(),
});

// Relations
export const appsRelations = relations(apps, ({ many }) => ({
  admobAccounts: many(admobAccounts),
  notifications: many(notifications),
  devices: many(devices),
  switchingRule: many(switchingRules),
  analyticsEvents: many(analyticsEvents),
}));

export const admobAccountsRelations = relations(admobAccounts, ({ one, many }) => ({
  app: one(apps, {
    fields: [admobAccounts.appId],
    references: [apps.id],
  }),
  analyticsEvents: many(analyticsEvents),
}));

export const switchingRulesRelations = relations(switchingRules, ({ one }) => ({
  app: one(apps, {
    fields: [switchingRules.appId],
    references: [apps.id],
  }),
}));

export const notificationsRelations = relations(notifications, ({ one, many }) => ({
  app: one(apps, {
    fields: [notifications.appId],
    references: [apps.id],
  }),
  events: many(notificationEvents),
}));

export const devicesRelations = relations(devices, ({ one }) => ({
  app: one(apps, {
    fields: [devices.appId],
    references: [apps.id],
  }),
}));

export const analyticsEventsRelations = relations(analyticsEvents, ({ one }) => ({
  app: one(apps, {
    fields: [analyticsEvents.appId],
    references: [apps.id],
  }),
  account: one(admobAccounts, {
    fields: [analyticsEvents.accountId],
    references: [admobAccounts.id],
  }),
}));

export const notificationEventsRelations = relations(notificationEvents, ({ one }) => ({
  notification: one(notifications, {
    fields: [notificationEvents.notificationId],
    references: [notifications.id],
  }),
  device: one(devices, {
    fields: [notificationEvents.deviceId],
    references: [devices.id],
  }),
}));

// Insert Schemas
export const insertAdminUserSchema = createInsertSchema(adminUsers).omit({
  id: true,
  createdAt: true,
});

export const insertAppSchema = createInsertSchema(apps).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const insertAdmobAccountSchema = createInsertSchema(admobAccounts).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const insertSwitchingRuleSchema = createInsertSchema(switchingRules).omit({
  id: true,
  updatedAt: true,
});

export const insertNotificationSchema = createInsertSchema(notifications).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const insertDeviceSchema = createInsertSchema(devices).omit({
  id: true,
  createdAt: true,
  lastSeen: true,
});

export const insertAnalyticsEventSchema = createInsertSchema(analyticsEvents).omit({
  id: true,
  timestamp: true,
});

export const insertNotificationEventSchema = createInsertSchema(notificationEvents).omit({
  id: true,
  timestamp: true,
});

// Types
export type InsertAdminUser = z.infer<typeof insertAdminUserSchema>;
export type AdminUser = typeof adminUsers.$inferSelect;

export type InsertApp = z.infer<typeof insertAppSchema>;
export type App = typeof apps.$inferSelect;

export type InsertAdmobAccount = z.infer<typeof insertAdmobAccountSchema>;
export type AdmobAccount = typeof admobAccounts.$inferSelect;

export type InsertSwitchingRule = z.infer<typeof insertSwitchingRuleSchema>;
export type SwitchingRule = typeof switchingRules.$inferSelect;

export type InsertNotification = z.infer<typeof insertNotificationSchema>;
export type Notification = typeof notifications.$inferSelect;

export type InsertDevice = z.infer<typeof insertDeviceSchema>;
export type Device = typeof devices.$inferSelect;

export type InsertAnalyticsEvent = z.infer<typeof insertAnalyticsEventSchema>;
export type AnalyticsEvent = typeof analyticsEvents.$inferSelect;

export type InsertNotificationEvent = z.infer<typeof insertNotificationEventSchema>;
export type NotificationEvent = typeof notificationEvents.$inferSelect;

// Legacy user type for compatibility
export const users = adminUsers;
export const insertUserSchema = insertAdminUserSchema;
export type InsertUser = InsertAdminUser;
export type User = AdminUser;
