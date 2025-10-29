import { sql } from "drizzle-orm";
import { pgTable, text, varchar, timestamp, integer, boolean, jsonb } from "drizzle-orm/pg-core";
import { relations } from "drizzle-orm";
import { createInsertSchema, createSelectSchema } from "drizzle-zod";
import { z } from "zod";

// Admin Users
export const adminUsers = pgTable("admin_users", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  username: text("username").notNull().unique(),
  password: text("password").notNull(),
  role: text("role").notNull().default("viewer"), // super_admin, admin, viewer
  createdAt: timestamp("created_at").defaultNow().notNull(),
});

// Android Apps
export const apps = pgTable("apps", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  packageName: text("package_name").notNull().unique(),
  appName: text("app_name").notNull(),
  description: text("description"),
  status: text("status").notNull().default("active"), // active, paused, disabled
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// AdMob Accounts
export const admobAccounts = pgTable("admob_accounts", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  appId: varchar("app_id").notNull().references(() => apps.id, { onDelete: "cascade" }),
  accountName: text("account_name").notNull(),
  status: text("status").notNull().default("active"), // active, paused, disabled
  priority: integer("priority").notNull().default(1),
  weight: integer("weight").notNull().default(50), // percentage for weighted distribution
  bannerId: text("banner_id"),
  interstitialId: text("interstitial_id"),
  rewardedId: text("rewarded_id"),
  appOpenId: text("app_open_id"),
  nativeId: text("native_id"),
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// Switching Rules for Apps
export const switchingRules = pgTable("switching_rules", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  appId: varchar("app_id").notNull().references(() => apps.id, { onDelete: "cascade" }).unique(),
  strategy: text("strategy").notNull().default("weighted_random"), // weighted_random, sequential, geographic, time_based
  rotationInterval: text("rotation_interval").notNull().default("daily"), // hourly, daily, session, random
  fallbackEnabled: boolean("fallback_enabled").notNull().default(true),
  abTestingEnabled: boolean("ab_testing_enabled").notNull().default(false),
  geographicRules: jsonb("geographic_rules"), // Array of {countries: string[], accountId: string}
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// Push Notifications
export const notifications = pgTable("notifications", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  appId: varchar("app_id").notNull().references(() => apps.id, { onDelete: "cascade" }),
  title: text("title").notNull(),
  message: text("message").notNull(),
  type: text("type").notNull().default("popup"), // popup, toast, banner, fullscreen
  priority: text("priority").notNull().default("normal"), // low, normal, high
  status: text("status").notNull().default("draft"), // draft, scheduled, sent, failed
  
  // Targeting
  targetCountries: text("target_countries").array(), // Array of country codes
  targetAppVersions: text("target_app_versions").array(),
  minAndroidVersion: integer("min_android_version"),
  userSegments: text("user_segments").array(), // new_users, active_users, inactive_users
  
  // Scheduling
  startDate: timestamp("start_date"),
  endDate: timestamp("end_date"),
  recurring: boolean("recurring").notNull().default(false),
  frequency: text("frequency").notNull().default("once"), // once, daily, weekly
  
  // Content
  imageUrl: text("image_url"),
  actionButtonText: text("action_button_text"),
  actionType: text("action_type"), // deeplink, url, dismiss
  actionValue: text("action_value"),
  cancelable: boolean("cancelable").notNull().default(true),
  
  // Display Rules
  maxDisplays: integer("max_displays").notNull().default(1),
  displayIntervalHours: integer("display_interval_hours").notNull().default(24),
  showOnAppLaunch: boolean("show_on_app_launch").notNull().default(false),
  
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull(),
});

// Device Registrations
export const devices = pgTable("devices", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  appId: varchar("app_id").notNull().references(() => apps.id, { onDelete: "cascade" }),
  fcmToken: text("fcm_token").notNull(),
  country: text("country"),
  appVersion: text("app_version"),
  androidVersion: integer("android_version"),
  deviceManufacturer: text("device_manufacturer"),
  deviceModel: text("device_model"),
  lastSeen: timestamp("last_seen").defaultNow().notNull(),
  createdAt: timestamp("created_at").defaultNow().notNull(),
});

// Analytics Events
export const analyticsEvents = pgTable("analytics_events", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  appId: varchar("app_id").notNull().references(() => apps.id, { onDelete: "cascade" }),
  accountId: varchar("account_id").references(() => admobAccounts.id, { onDelete: "set null" }),
  eventType: text("event_type").notNull(), // impression, click, revenue
  adType: text("ad_type"), // banner, interstitial, rewarded, app_open, native
  value: integer("value"), // For revenue tracking (in cents)
  country: text("country"),
  timestamp: timestamp("timestamp").defaultNow().notNull(),
});

// Notification Events
export const notificationEvents = pgTable("notification_events", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  notificationId: varchar("notification_id").notNull().references(() => notifications.id, { onDelete: "cascade" }),
  deviceId: varchar("device_id").references(() => devices.id, { onDelete: "set null" }),
  eventType: text("event_type").notNull(), // displayed, clicked, dismissed
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
export const insertAdminUserSchema = createInsertSchema(adminUsers).pick({
  username: true,
  password: true,
  role: true,
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
