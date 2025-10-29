import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { Settings as SettingsIcon, Save } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
  FormDescription,
} from "@/components/ui/form";
import { Switch } from "@/components/ui/switch";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { insertSwitchingRuleSchema, type App, type SwitchingRule } from "@shared/schema";
import { useToast } from "@/hooks/use-toast";
import { apiRequest, queryClient } from "@/lib/queryClient";
import { Textarea } from "@/components/ui/textarea";

const geographicRuleSchema = z.object({
  countries: z.string(),
  accountId: z.string(),
});

export default function Settings() {
  const { toast } = useToast();
  const [selectedApp, setSelectedApp] = useState<string>("");

  const { data: apps } = useQuery<App[]>({
    queryKey: ["/api/admin/apps"],
  });

  const { data: switchingRules } = useQuery<SwitchingRule[]>({
    queryKey: ["/api/admin/switching-rules"],
  });

  const currentRule = switchingRules?.find((rule) => rule.appId === selectedApp);

  const form = useForm({
    resolver: zodResolver(insertSwitchingRuleSchema),
    defaultValues: {
      appId: "",
      strategy: "weighted_random",
      rotationInterval: "daily",
      fallbackEnabled: true,
      abTestingEnabled: false,
      geographicRules: null,
    },
  });

  const updateMutation = useMutation({
    mutationFn: async (data: any) => {
      if (currentRule) {
        return await apiRequest("PUT", `/api/admin/switching-rules/${currentRule.id}`, data);
      }
      return await apiRequest("POST", "/api/admin/switching-rules", data);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/admin/switching-rules"] });
      toast({
        title: "Settings updated",
        description: "Switching rules have been updated successfully.",
      });
    },
    onError: (error: Error) => {
      toast({
        title: "Error",
        description: error.message,
        variant: "destructive",
      });
    },
  });

  // Update form when app or rule changes
  useState(() => {
    if (currentRule) {
      form.reset({
        appId: currentRule.appId,
        strategy: currentRule.strategy,
        rotationInterval: currentRule.rotationInterval,
        fallbackEnabled: currentRule.fallbackEnabled,
        abTestingEnabled: currentRule.abTestingEnabled,
        geographicRules: currentRule.geographicRules,
      });
    } else if (selectedApp) {
      form.reset({
        appId: selectedApp,
        strategy: "weighted_random",
        rotationInterval: "daily",
        fallbackEnabled: true,
        abTestingEnabled: false,
        geographicRules: null,
      });
    }
  });

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-medium mb-2">Settings</h1>
        <p className="text-muted-foreground">
          Configure AdMob switching strategies and platform settings
        </p>
      </div>

      <div className="grid gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Select App</CardTitle>
            <CardDescription>
              Choose an app to configure switching rules
            </CardDescription>
          </CardHeader>
          <CardContent>
            <Select value={selectedApp} onValueChange={setSelectedApp}>
              <SelectTrigger className="w-full" data-testid="select-app">
                <SelectValue placeholder="Select an app" />
              </SelectTrigger>
              <SelectContent>
                {apps?.map((app) => (
                  <SelectItem key={app.id} value={app.id}>
                    {app.appName} ({app.packageName})
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </CardContent>
        </Card>

        {selectedApp && (
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle>AdMob Switching Configuration</CardTitle>
                  <CardDescription>
                    Configure how AdMob accounts are switched for this app
                  </CardDescription>
                </div>
                <Button
                  onClick={() => updateMutation.mutate(form.getValues())}
                  disabled={updateMutation.isPending}
                  data-testid="button-save-settings"
                >
                  <Save className="h-4 w-4 mr-2" />
                  {updateMutation.isPending ? "Saving..." : "Save Changes"}
                </Button>
              </div>
            </CardHeader>
            <CardContent>
              <Form {...form}>
                <form className="space-y-6">
                  <div className="grid gap-6 md:grid-cols-2">
                    <FormField
                      control={form.control}
                      name="strategy"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Switching Strategy</FormLabel>
                          <Select onValueChange={field.onChange} value={field.value}>
                            <FormControl>
                              <SelectTrigger data-testid="select-strategy">
                                <SelectValue placeholder="Select strategy" />
                              </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                              <SelectItem value="weighted_random">
                                Weighted Random
                              </SelectItem>
                              <SelectItem value="sequential">
                                Sequential Rotation
                              </SelectItem>
                              <SelectItem value="geographic">
                                Geographic Targeting
                              </SelectItem>
                              <SelectItem value="time_based">
                                Time-Based
                              </SelectItem>
                            </SelectContent>
                          </Select>
                          <FormDescription>
                            How AdMob accounts are selected for ad requests
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="rotationInterval"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Rotation Interval</FormLabel>
                          <Select onValueChange={field.onChange} value={field.value}>
                            <FormControl>
                              <SelectTrigger data-testid="select-interval">
                                <SelectValue placeholder="Select interval" />
                              </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                              <SelectItem value="hourly">Hourly</SelectItem>
                              <SelectItem value="daily">Daily</SelectItem>
                              <SelectItem value="session">Per Session</SelectItem>
                              <SelectItem value="random">Random</SelectItem>
                            </SelectContent>
                          </Select>
                          <FormDescription>
                            How often to rotate between accounts
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  <div className="space-y-4">
                    <FormField
                      control={form.control}
                      name="fallbackEnabled"
                      render={({ field }) => (
                        <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                          <div className="space-y-0.5">
                            <FormLabel className="text-base">
                              Fallback Enabled
                            </FormLabel>
                            <FormDescription>
                              Automatically switch to backup accounts if primary fails
                            </FormDescription>
                          </div>
                          <FormControl>
                            <Switch
                              checked={field.value}
                              onCheckedChange={field.onChange}
                              data-testid="switch-fallback"
                            />
                          </FormControl>
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="abTestingEnabled"
                      render={({ field }) => (
                        <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                          <div className="space-y-0.5">
                            <FormLabel className="text-base">
                              A/B Testing Enabled
                            </FormLabel>
                            <FormDescription>
                              Split traffic to compare account performance
                            </FormDescription>
                          </div>
                          <FormControl>
                            <Switch
                              checked={field.value}
                              onCheckedChange={field.onChange}
                              data-testid="switch-ab-testing"
                            />
                          </FormControl>
                        </FormItem>
                      )}
                    />
                  </div>

                  {form.watch("strategy") === "geographic" && (
                    <Card className="border-primary/20">
                      <CardHeader>
                        <CardTitle className="text-base">Geographic Rules</CardTitle>
                        <CardDescription>
                          Define which AdMob accounts to use for specific countries
                        </CardDescription>
                      </CardHeader>
                      <CardContent>
                        <div className="space-y-3 text-sm">
                          <p className="text-muted-foreground">
                            Configure geographic targeting in JSON format:
                          </p>
                          <Textarea
                            placeholder={`[\n  {\n    "countries": ["US", "CA", "UK"],\n    "accountId": "account-id-1"\n  },\n  {\n    "countries": ["IN", "PK", "BD"],\n    "accountId": "account-id-2"\n  }\n]`}
                            className="font-mono text-xs"
                            rows={8}
                            data-testid="input-geographic-rules"
                          />
                        </div>
                      </CardContent>
                    </Card>
                  )}
                </form>
              </Form>
            </CardContent>
          </Card>
        )}

        <Card>
          <CardHeader>
            <CardTitle>API Documentation</CardTitle>
            <CardDescription>
              Integration endpoints for Android apps
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="p-4 rounded-lg bg-muted/50 border border-border">
                <p className="text-sm font-medium mb-2">Configuration Endpoint</p>
                <code className="text-xs font-mono">
                  GET /api/v1/config/&#123;package_name&#125;
                </code>
                <p className="text-xs text-muted-foreground mt-2">
                  Returns AdMob accounts and switching rules for your app
                </p>
              </div>
              <div className="p-4 rounded-lg bg-muted/50 border border-border">
                <p className="text-sm font-medium mb-2">Analytics Tracking</p>
                <code className="text-xs font-mono">
                  POST /api/v1/analytics/admob
                </code>
                <p className="text-xs text-muted-foreground mt-2">
                  Send impression, click, and revenue events
                </p>
              </div>
              <div className="p-4 rounded-lg bg-muted/50 border border-border">
                <p className="text-sm font-medium mb-2">Device Registration</p>
                <code className="text-xs font-mono">
                  POST /api/v1/device/register
                </code>
                <p className="text-xs text-muted-foreground mt-2">
                  Register device for push notifications
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
