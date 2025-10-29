import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { Plus, Bell, Trash2, Send, Calendar } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogFooter,
} from "@/components/ui/dialog";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
  FormDescription,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Switch } from "@/components/ui/switch";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { insertNotificationSchema, type Notification, type InsertNotification, type App } from "@shared/schema";
import { useToast } from "@/hooks/use-toast";
import { apiRequest, queryClient } from "@/lib/queryClient";
import { Skeleton } from "@/components/ui/skeleton";
import { z } from "zod";

const notificationFormSchema = insertNotificationSchema.extend({
  targetCountries: z.string().optional(),
  targetAppVersions: z.string().optional(),
  userSegments: z.string().optional(),
});

type NotificationFormData = z.infer<typeof notificationFormSchema>;

export default function Notifications() {
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const { toast } = useToast();

  const { data: apps } = useQuery<App[]>({
    queryKey: ["/api/admin/apps"],
  });

  const { data: notifications, isLoading } = useQuery<Notification[]>({
    queryKey: ["/api/admin/notifications"],
  });

  const form = useForm<NotificationFormData>({
    resolver: zodResolver(notificationFormSchema),
    defaultValues: {
      appId: "",
      title: "",
      message: "",
      type: "popup",
      priority: "normal",
      status: "draft",
      targetCountries: "",
      targetAppVersions: "",
      minAndroidVersion: 21,
      userSegments: "",
      recurring: false,
      frequency: "once",
      actionButtonText: "",
      actionType: "dismiss",
      actionValue: "",
      cancelable: true,
      maxDisplays: 1,
      displayIntervalHours: 24,
      showOnAppLaunch: false,
    },
  });

  const createMutation = useMutation({
    mutationFn: async (data: NotificationFormData) => {
      const payload: InsertNotification = {
        ...data,
        targetCountries: data.targetCountries
          ? data.targetCountries.split(",").map((s) => s.trim()).filter(Boolean)
          : undefined,
        targetAppVersions: data.targetAppVersions
          ? data.targetAppVersions.split(",").map((s) => s.trim()).filter(Boolean)
          : undefined,
        userSegments: data.userSegments
          ? data.userSegments.split(",").map((s) => s.trim()).filter(Boolean)
          : undefined,
      };
      return await apiRequest("POST", "/api/admin/notifications", payload);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/admin/notifications"] });
      toast({
        title: "Notification created",
        description: `${form.getValues().title} has been created successfully.`,
      });
      setIsDialogOpen(false);
      form.reset();
    },
    onError: (error: Error) => {
      toast({
        title: "Error",
        description: error.message,
        variant: "destructive",
      });
    },
  });

  const deleteMutation = useMutation({
    mutationFn: async (id: string) => {
      return await apiRequest("DELETE", `/api/admin/notifications/${id}`, {});
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/admin/notifications"] });
      toast({
        title: "Notification deleted",
        description: "The notification has been deleted successfully.",
      });
    },
  });

  const sendMutation = useMutation({
    mutationFn: async (id: string) => {
      return await apiRequest("POST", `/api/admin/notifications/${id}/send`, {});
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/admin/notifications"] });
      toast({
        title: "Notification sent",
        description: "The notification has been scheduled for delivery.",
      });
    },
  });

  const getAppName = (appId: string) => {
    return apps?.find((app) => app.id === appId)?.appName || "Unknown App";
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-medium mb-2">Notifications</h1>
          <p className="text-muted-foreground">
            Create and manage push notifications for your apps
          </p>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button data-testid="button-create-notification">
              <Plus className="h-4 w-4 mr-2" />
              Create Notification
            </Button>
          </DialogTrigger>
          <DialogContent className="sm:max-w-[700px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>Create Push Notification</DialogTitle>
              <DialogDescription>
                Create a targeted push notification for your app users.
              </DialogDescription>
            </DialogHeader>
            <Form {...form}>
              <form
                onSubmit={form.handleSubmit((data) => createMutation.mutate(data))}
                className="space-y-4"
              >
                <Tabs defaultValue="content" className="w-full">
                  <TabsList className="grid w-full grid-cols-3">
                    <TabsTrigger value="content">Content</TabsTrigger>
                    <TabsTrigger value="targeting">Targeting</TabsTrigger>
                    <TabsTrigger value="display">Display</TabsTrigger>
                  </TabsList>
                  <TabsContent value="content" className="space-y-4 mt-4">
                    <FormField
                      control={form.control}
                      name="appId"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>App</FormLabel>
                          <Select onValueChange={field.onChange} defaultValue={field.value}>
                            <FormControl>
                              <SelectTrigger data-testid="select-app">
                                <SelectValue placeholder="Select app" />
                              </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                              {apps?.map((app) => (
                                <SelectItem key={app.id} value={app.id}>
                                  {app.appName}
                                </SelectItem>
                              ))}
                            </SelectContent>
                          </Select>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="title"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Title</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="Special Offer!"
                              data-testid="input-title"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="message"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Message</FormLabel>
                          <FormControl>
                            <Textarea
                              placeholder="Get 50% off on premium features..."
                              data-testid="input-message"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <div className="grid grid-cols-2 gap-4">
                      <FormField
                        control={form.control}
                        name="type"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Type</FormLabel>
                            <Select onValueChange={field.onChange} defaultValue={field.value}>
                              <FormControl>
                                <SelectTrigger data-testid="select-type">
                                  <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                <SelectItem value="popup">Popup</SelectItem>
                                <SelectItem value="toast">Toast</SelectItem>
                                <SelectItem value="banner">Banner</SelectItem>
                                <SelectItem value="fullscreen">Fullscreen</SelectItem>
                              </SelectContent>
                            </Select>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="priority"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Priority</FormLabel>
                            <Select onValueChange={field.onChange} defaultValue={field.value}>
                              <FormControl>
                                <SelectTrigger data-testid="select-priority">
                                  <SelectValue placeholder="Select priority" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                <SelectItem value="low">Low</SelectItem>
                                <SelectItem value="normal">Normal</SelectItem>
                                <SelectItem value="high">High</SelectItem>
                              </SelectContent>
                            </Select>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>
                    <FormField
                      control={form.control}
                      name="actionButtonText"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Action Button Text (Optional)</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="Claim Now"
                              data-testid="input-action-text"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </TabsContent>
                  <TabsContent value="targeting" className="space-y-4 mt-4">
                    <FormField
                      control={form.control}
                      name="targetCountries"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Target Countries (Optional)</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="US, CA, UK (comma-separated)"
                              data-testid="input-countries"
                              {...field}
                            />
                          </FormControl>
                          <FormDescription>
                            Leave empty to target all countries
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="targetAppVersions"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Target App Versions (Optional)</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="1.0.0, 1.1.0 (comma-separated)"
                              data-testid="input-versions"
                              {...field}
                            />
                          </FormControl>
                          <FormDescription>
                            Leave empty to target all versions
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="minAndroidVersion"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Min Android Version (API Level)</FormLabel>
                          <FormControl>
                            <Input
                              type="number"
                              min="21"
                              data-testid="input-min-android"
                              {...field}
                              onChange={(e) => field.onChange(parseInt(e.target.value))}
                            />
                          </FormControl>
                          <FormDescription>
                            Minimum Android API level (e.g., 21 for Android 5.0)
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="userSegments"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>User Segments (Optional)</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="new_users, active_users (comma-separated)"
                              data-testid="input-segments"
                              {...field}
                            />
                          </FormControl>
                          <FormDescription>
                            Leave empty to target all users
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </TabsContent>
                  <TabsContent value="display" className="space-y-4 mt-4">
                    <div className="grid grid-cols-2 gap-4">
                      <FormField
                        control={form.control}
                        name="maxDisplays"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Max Displays</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                min="1"
                                data-testid="input-max-displays"
                                {...field}
                                onChange={(e) => field.onChange(parseInt(e.target.value))}
                              />
                            </FormControl>
                            <FormDescription>
                              How many times to show per user
                            </FormDescription>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="displayIntervalHours"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Display Interval (hours)</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                min="1"
                                data-testid="input-interval"
                                {...field}
                                onChange={(e) => field.onChange(parseInt(e.target.value))}
                              />
                            </FormControl>
                            <FormDescription>
                              Time between displays
                            </FormDescription>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>
                    <FormField
                      control={form.control}
                      name="showOnAppLaunch"
                      render={({ field }) => (
                        <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                          <div className="space-y-0.5">
                            <FormLabel className="text-base">Show on App Launch</FormLabel>
                            <FormDescription>
                              Display notification when app starts
                            </FormDescription>
                          </div>
                          <FormControl>
                            <Switch
                              checked={field.value}
                              onCheckedChange={field.onChange}
                              data-testid="switch-app-launch"
                            />
                          </FormControl>
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="cancelable"
                      render={({ field }) => (
                        <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                          <div className="space-y-0.5">
                            <FormLabel className="text-base">Cancelable</FormLabel>
                            <FormDescription>
                              Allow users to dismiss the notification
                            </FormDescription>
                          </div>
                          <FormControl>
                            <Switch
                              checked={field.value}
                              onCheckedChange={field.onChange}
                              data-testid="switch-cancelable"
                            />
                          </FormControl>
                        </FormItem>
                      )}
                    />
                  </TabsContent>
                </Tabs>
                <DialogFooter>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => setIsDialogOpen(false)}
                    data-testid="button-cancel"
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    disabled={createMutation.isPending}
                    data-testid="button-save-notification"
                  >
                    {createMutation.isPending ? "Creating..." : "Create Notification"}
                  </Button>
                </DialogFooter>
              </form>
            </Form>
          </DialogContent>
        </Dialog>
      </div>

      {isLoading ? (
        <div className="grid gap-4 md:grid-cols-2">
          {[1, 2].map((i) => (
            <Card key={i}>
              <CardHeader>
                <Skeleton className="h-6 w-3/4 mb-2" />
                <Skeleton className="h-4 w-1/2" />
              </CardHeader>
              <CardContent>
                <Skeleton className="h-4 w-full mb-2" />
                <Skeleton className="h-4 w-5/6" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : notifications && notifications.length > 0 ? (
        <div className="grid gap-4 md:grid-cols-2">
          {notifications.map((notification) => (
            <Card
              key={notification.id}
              className="hover-elevate transition-all"
              data-testid={`card-notification-${notification.id}`}
            >
              <CardHeader>
                <div className="flex items-start justify-between gap-2">
                  <div className="flex items-center gap-3 flex-1 min-w-0">
                    <div className="p-2 rounded-md bg-chart-3/10 shrink-0">
                      <Bell className="h-5 w-5 text-chart-3" />
                    </div>
                    <div className="flex-1 min-w-0">
                      <CardTitle className="text-base truncate" data-testid={`text-title-${notification.id}`}>
                        {notification.title}
                      </CardTitle>
                      <p className="text-xs text-muted-foreground truncate">
                        {getAppName(notification.appId)}
                      </p>
                    </div>
                  </div>
                  <div className="flex gap-2 shrink-0">
                    {notification.status === "draft" && (
                      <Button
                        size="icon"
                        variant="ghost"
                        className="h-8 w-8"
                        onClick={() => sendMutation.mutate(notification.id)}
                        data-testid={`button-send-${notification.id}`}
                      >
                        <Send className="h-4 w-4" />
                      </Button>
                    )}
                    <Button
                      size="icon"
                      variant="ghost"
                      className="h-8 w-8"
                      onClick={() => deleteMutation.mutate(notification.id)}
                      data-testid={`button-delete-${notification.id}`}
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </CardHeader>
              <CardContent className="space-y-3">
                <p className="text-sm line-clamp-2">{notification.message}</p>
                <div className="flex flex-wrap gap-2">
                  <Badge variant={notification.status === "sent" ? "default" : "secondary"}>
                    {notification.status}
                  </Badge>
                  <Badge variant="outline" className="capitalize">
                    {notification.type}
                  </Badge>
                  <Badge variant="outline" className="capitalize">
                    {notification.priority}
                  </Badge>
                </div>
                <div className="flex items-center justify-between text-xs text-muted-foreground">
                  <span className="flex items-center gap-1">
                    <Calendar className="h-3 w-3" />
                    {new Date(notification.createdAt).toLocaleDateString()}
                  </span>
                  {notification.targetCountries && notification.targetCountries.length > 0 && (
                    <span>🌍 {notification.targetCountries.join(", ")}</span>
                  )}
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : (
        <Card>
          <CardContent className="flex flex-col items-center justify-center py-12">
            <div className="p-4 rounded-full bg-muted/50 mb-4">
              <Bell className="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 className="text-lg font-medium mb-2">No notifications yet</h3>
            <p className="text-sm text-muted-foreground mb-4 text-center max-w-sm">
              Create your first push notification to engage with your app users.
            </p>
            <Button onClick={() => setIsDialogOpen(true)} data-testid="button-add-first-notification">
              <Plus className="h-4 w-4 mr-2" />
              Create Your First Notification
            </Button>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
