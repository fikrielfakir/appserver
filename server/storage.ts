// Referenced from javascript_database blueprint
import {
  adminUsers,
  apps,
  admobAccounts,
  switchingRules,
  notifications,
  devices,
  analyticsEvents,
  notificationEvents,
  type AdminUser,
  type InsertAdminUser,
  type App,
  type InsertApp,
  type AdmobAccount,
  type InsertAdmobAccount,
  type SwitchingRule,
  type InsertSwitchingRule,
  type Notification,
  type InsertNotification,
  type Device,
  type InsertDevice,
  type AnalyticsEvent,
  type InsertAnalyticsEvent,
  type NotificationEvent,
  type InsertNotificationEvent,
} from "@shared/schema";
import { db } from "./db";
import { eq, desc, and, sql } from "drizzle-orm";

export interface IStorage {
  // Admin Users
  getAdminUser(id: string): Promise<AdminUser | undefined>;
  getAdminUserByUsername(username: string): Promise<AdminUser | undefined>;
  createAdminUser(user: InsertAdminUser): Promise<AdminUser>;
  
  // Apps
  getAllApps(): Promise<App[]>;
  getApp(id: string): Promise<App | undefined>;
  getAppByPackageName(packageName: string): Promise<App | undefined>;
  createApp(app: InsertApp): Promise<App>;
  updateApp(id: string, app: Partial<InsertApp>): Promise<App | undefined>;
  deleteApp(id: string): Promise<void>;
  
  // AdMob Accounts
  getAllAdmobAccounts(): Promise<AdmobAccount[]>;
  getAdmobAccountsByApp(appId: string): Promise<AdmobAccount[]>;
  getAdmobAccount(id: string): Promise<AdmobAccount | undefined>;
  createAdmobAccount(account: InsertAdmobAccount): Promise<AdmobAccount>;
  updateAdmobAccount(id: string, account: Partial<InsertAdmobAccount>): Promise<AdmobAccount | undefined>;
  deleteAdmobAccount(id: string): Promise<void>;
  
  // Switching Rules
  getAllSwitchingRules(): Promise<SwitchingRule[]>;
  getSwitchingRuleByApp(appId: string): Promise<SwitchingRule | undefined>;
  getSwitchingRule(id: string): Promise<SwitchingRule | undefined>;
  createSwitchingRule(rule: InsertSwitchingRule): Promise<SwitchingRule>;
  updateSwitchingRule(id: string, rule: Partial<InsertSwitchingRule>): Promise<SwitchingRule | undefined>;
  deleteSwitchingRule(id: string): Promise<void>;
  
  // Notifications
  getAllNotifications(): Promise<Notification[]>;
  getNotificationsByApp(appId: string): Promise<Notification[]>;
  getNotification(id: string): Promise<Notification | undefined>;
  createNotification(notification: InsertNotification): Promise<Notification>;
  updateNotification(id: string, notification: Partial<InsertNotification>): Promise<Notification | undefined>;
  deleteNotification(id: string): Promise<void>;
  
  // Devices
  getAllDevices(): Promise<Device[]>;
  getDevicesByApp(appId: string): Promise<Device[]>;
  getDevice(id: string): Promise<Device | undefined>;
  createDevice(device: InsertDevice): Promise<Device>;
  updateDevice(id: string, device: Partial<InsertDevice>): Promise<Device | undefined>;
  deleteDevice(id: string): Promise<void>;
  
  // Analytics
  createAnalyticsEvent(event: InsertAnalyticsEvent): Promise<AnalyticsEvent>;
  getAnalyticsEventsByApp(appId: string, limit?: number): Promise<AnalyticsEvent[]>;
  
  // Notification Events
  createNotificationEvent(event: InsertNotificationEvent): Promise<NotificationEvent>;
  getNotificationEventsByNotification(notificationId: string): Promise<NotificationEvent[]>;
  
  // Dashboard Stats
  getDashboardStats(): Promise<{
    totalApps: number;
    totalAdmobAccounts: number;
    totalNotifications: number;
    totalDevices: number;
  }>;
}

export class DatabaseStorage implements IStorage {
  // Admin Users
  async getAdminUser(id: string): Promise<AdminUser | undefined> {
    const [user] = await db.select().from(adminUsers).where(eq(adminUsers.id, id));
    return user || undefined;
  }

  async getAdminUserByUsername(username: string): Promise<AdminUser | undefined> {
    const [user] = await db.select().from(adminUsers).where(eq(adminUsers.username, username));
    return user || undefined;
  }

  async createAdminUser(insertUser: InsertAdminUser): Promise<AdminUser> {
    const [user] = await db.insert(adminUsers).values(insertUser).returning();
    return user;
  }

  // Apps
  async getAllApps(): Promise<App[]> {
    return await db.select().from(apps).orderBy(desc(apps.createdAt));
  }

  async getApp(id: string): Promise<App | undefined> {
    const [app] = await db.select().from(apps).where(eq(apps.id, id));
    return app || undefined;
  }

  async getAppByPackageName(packageName: string): Promise<App | undefined> {
    const [app] = await db.select().from(apps).where(eq(apps.packageName, packageName));
    return app || undefined;
  }

  async createApp(insertApp: InsertApp): Promise<App> {
    const [app] = await db.insert(apps).values(insertApp).returning();
    return app;
  }

  async updateApp(id: string, updateData: Partial<InsertApp>): Promise<App | undefined> {
    const [app] = await db
      .update(apps)
      .set({ ...updateData, updatedAt: new Date() })
      .where(eq(apps.id, id))
      .returning();
    return app || undefined;
  }

  async deleteApp(id: string): Promise<void> {
    await db.delete(apps).where(eq(apps.id, id));
  }

  // AdMob Accounts
  async getAllAdmobAccounts(): Promise<AdmobAccount[]> {
    return await db.select().from(admobAccounts).orderBy(desc(admobAccounts.createdAt));
  }

  async getAdmobAccountsByApp(appId: string): Promise<AdmobAccount[]> {
    return await db.select().from(admobAccounts).where(eq(admobAccounts.appId, appId));
  }

  async getAdmobAccount(id: string): Promise<AdmobAccount | undefined> {
    const [account] = await db.select().from(admobAccounts).where(eq(admobAccounts.id, id));
    return account || undefined;
  }

  async createAdmobAccount(insertAccount: InsertAdmobAccount): Promise<AdmobAccount> {
    const [account] = await db.insert(admobAccounts).values(insertAccount).returning();
    return account;
  }

  async updateAdmobAccount(id: string, updateData: Partial<InsertAdmobAccount>): Promise<AdmobAccount | undefined> {
    const [account] = await db
      .update(admobAccounts)
      .set({ ...updateData, updatedAt: new Date() })
      .where(eq(admobAccounts.id, id))
      .returning();
    return account || undefined;
  }

  async deleteAdmobAccount(id: string): Promise<void> {
    await db.delete(admobAccounts).where(eq(admobAccounts.id, id));
  }

  // Switching Rules
  async getAllSwitchingRules(): Promise<SwitchingRule[]> {
    return await db.select().from(switchingRules).orderBy(desc(switchingRules.updatedAt));
  }

  async getSwitchingRuleByApp(appId: string): Promise<SwitchingRule | undefined> {
    const [rule] = await db.select().from(switchingRules).where(eq(switchingRules.appId, appId));
    return rule || undefined;
  }

  async getSwitchingRule(id: string): Promise<SwitchingRule | undefined> {
    const [rule] = await db.select().from(switchingRules).where(eq(switchingRules.id, id));
    return rule || undefined;
  }

  async createSwitchingRule(insertRule: InsertSwitchingRule): Promise<SwitchingRule> {
    const [rule] = await db.insert(switchingRules).values(insertRule).returning();
    return rule;
  }

  async updateSwitchingRule(id: string, updateData: Partial<InsertSwitchingRule>): Promise<SwitchingRule | undefined> {
    const [rule] = await db
      .update(switchingRules)
      .set({ ...updateData, updatedAt: new Date() })
      .where(eq(switchingRules.id, id))
      .returning();
    return rule || undefined;
  }

  async deleteSwitchingRule(id: string): Promise<void> {
    await db.delete(switchingRules).where(eq(switchingRules.id, id));
  }

  // Notifications
  async getAllNotifications(): Promise<Notification[]> {
    return await db.select().from(notifications).orderBy(desc(notifications.createdAt));
  }

  async getNotificationsByApp(appId: string): Promise<Notification[]> {
    return await db.select().from(notifications).where(eq(notifications.appId, appId)).orderBy(desc(notifications.createdAt));
  }

  async getNotification(id: string): Promise<Notification | undefined> {
    const [notification] = await db.select().from(notifications).where(eq(notifications.id, id));
    return notification || undefined;
  }

  async createNotification(insertNotification: InsertNotification): Promise<Notification> {
    const [notification] = await db.insert(notifications).values(insertNotification).returning();
    return notification;
  }

  async updateNotification(id: string, updateData: Partial<InsertNotification>): Promise<Notification | undefined> {
    const [notification] = await db
      .update(notifications)
      .set({ ...updateData, updatedAt: new Date() })
      .where(eq(notifications.id, id))
      .returning();
    return notification || undefined;
  }

  async deleteNotification(id: string): Promise<void> {
    await db.delete(notifications).where(eq(notifications.id, id));
  }

  // Devices
  async getAllDevices(): Promise<Device[]> {
    return await db.select().from(devices).orderBy(desc(devices.lastSeen));
  }

  async getDevicesByApp(appId: string): Promise<Device[]> {
    return await db.select().from(devices).where(eq(devices.appId, appId)).orderBy(desc(devices.lastSeen));
  }

  async getDevice(id: string): Promise<Device | undefined> {
    const [device] = await db.select().from(devices).where(eq(devices.id, id));
    return device || undefined;
  }

  async createDevice(insertDevice: InsertDevice): Promise<Device> {
    const [device] = await db.insert(devices).values(insertDevice).returning();
    return device;
  }

  async updateDevice(id: string, updateData: Partial<InsertDevice>): Promise<Device | undefined> {
    const [device] = await db
      .update(devices)
      .set({ ...updateData, lastSeen: new Date() })
      .where(eq(devices.id, id))
      .returning();
    return device || undefined;
  }

  async deleteDevice(id: string): Promise<void> {
    await db.delete(devices).where(eq(devices.id, id));
  }

  // Analytics
  async createAnalyticsEvent(insertEvent: InsertAnalyticsEvent): Promise<AnalyticsEvent> {
    const [event] = await db.insert(analyticsEvents).values(insertEvent).returning();
    return event;
  }

  async getAnalyticsEventsByApp(appId: string, limit: number = 1000): Promise<AnalyticsEvent[]> {
    return await db
      .select()
      .from(analyticsEvents)
      .where(eq(analyticsEvents.appId, appId))
      .orderBy(desc(analyticsEvents.timestamp))
      .limit(limit);
  }

  // Notification Events
  async createNotificationEvent(insertEvent: InsertNotificationEvent): Promise<NotificationEvent> {
    const [event] = await db.insert(notificationEvents).values(insertEvent).returning();
    return event;
  }

  async getNotificationEventsByNotification(notificationId: string): Promise<NotificationEvent[]> {
    return await db
      .select()
      .from(notificationEvents)
      .where(eq(notificationEvents.notificationId, notificationId))
      .orderBy(desc(notificationEvents.timestamp));
  }

  // Dashboard Stats
  async getDashboardStats(): Promise<{
    totalApps: number;
    totalAdmobAccounts: number;
    totalNotifications: number;
    totalDevices: number;
  }> {
    const [appsCount] = await db.select({ count: sql<number>`count(*)::int` }).from(apps);
    const [accountsCount] = await db.select({ count: sql<number>`count(*)::int` }).from(admobAccounts);
    const [notificationsCount] = await db.select({ count: sql<number>`count(*)::int` }).from(notifications);
    const [devicesCount] = await db.select({ count: sql<number>`count(*)::int` }).from(devices);

    return {
      totalApps: appsCount?.count || 0,
      totalAdmobAccounts: accountsCount?.count || 0,
      totalNotifications: notificationsCount?.count || 0,
      totalDevices: devicesCount?.count || 0,
    };
  }
}

export const storage = new DatabaseStorage();
