import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { Plus, Pencil, Trash2, DollarSign, MoreVertical, Settings2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
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
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { insertAdmobAccountSchema, type AdmobAccount, type InsertAdmobAccount, type App } from "@shared/schema";
import { useToast } from "@/hooks/use-toast";
import { apiRequest, queryClient } from "@/lib/queryClient";
import { Skeleton } from "@/components/ui/skeleton";

export default function AdMob() {
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [editingAccount, setEditingAccount] = useState<AdmobAccount | null>(null);
  const { toast } = useToast();

  const { data: apps } = useQuery<App[]>({
    queryKey: ["/api/admin/apps"],
  });

  const { data: accounts, isLoading } = useQuery<AdmobAccount[]>({
    queryKey: ["/api/admin/admob-accounts"],
  });

  const form = useForm<InsertAdmobAccount>({
    resolver: zodResolver(insertAdmobAccountSchema),
    defaultValues: {
      appId: "",
      accountName: "",
      status: "active",
      priority: 1,
      weight: 50,
      bannerId: "",
      interstitialId: "",
      rewardedId: "",
      appOpenId: "",
      nativeId: "",
    },
  });

  const createMutation = useMutation({
    mutationFn: async (data: InsertAdmobAccount) => {
      if (editingAccount) {
        return await apiRequest("PUT", `/api/admin/admob-accounts/${editingAccount.id}`, data);
      }
      return await apiRequest("POST", "/api/admin/admob-accounts", data);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/admin/admob-accounts"] });
      toast({
        title: editingAccount ? "Account updated" : "Account created",
        description: `${form.getValues().accountName} has been ${editingAccount ? "updated" : "created"} successfully.`,
      });
      setIsDialogOpen(false);
      setEditingAccount(null);
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
      return await apiRequest("DELETE", `/api/admin/admob-accounts/${id}`, {});
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/admin/admob-accounts"] });
      toast({
        title: "Account deleted",
        description: "The AdMob account has been deleted successfully.",
      });
    },
  });

  const handleEdit = (account: AdmobAccount) => {
    setEditingAccount(account);
    form.reset({
      appId: account.appId,
      accountName: account.accountName,
      status: account.status,
      priority: account.priority,
      weight: account.weight,
      bannerId: account.bannerId || "",
      interstitialId: account.interstitialId || "",
      rewardedId: account.rewardedId || "",
      appOpenId: account.appOpenId || "",
      nativeId: account.nativeId || "",
    });
    setIsDialogOpen(true);
  };

  const handleCloseDialog = () => {
    setIsDialogOpen(false);
    setEditingAccount(null);
    form.reset();
  };

  const getAppName = (appId: string) => {
    return apps?.find((app) => app.id === appId)?.appName || "Unknown App";
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-medium mb-2">AdMob Accounts</h1>
          <p className="text-muted-foreground">
            Manage multi-account AdMob switching strategies
          </p>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button data-testid="button-create-admob" onClick={() => form.reset()}>
              <Plus className="h-4 w-4 mr-2" />
              Add Account
            </Button>
          </DialogTrigger>
          <DialogContent className="sm:max-w-[650px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>
                {editingAccount ? "Edit AdMob Account" : "Create AdMob Account"}
              </DialogTitle>
              <DialogDescription>
                {editingAccount
                  ? "Update the AdMob account details below."
                  : "Add a new AdMob account for multi-account switching."}
              </DialogDescription>
            </DialogHeader>
            <Form {...form}>
              <form
                onSubmit={form.handleSubmit((data) => createMutation.mutate(data))}
                className="space-y-4"
              >
                <Tabs defaultValue="basic" className="w-full">
                  <TabsList className="grid w-full grid-cols-2">
                    <TabsTrigger value="basic">Basic Info</TabsTrigger>
                    <TabsTrigger value="adunits">Ad Units</TabsTrigger>
                  </TabsList>
                  <TabsContent value="basic" className="space-y-4 mt-4">
                    <FormField
                      control={form.control}
                      name="appId"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>App</FormLabel>
                          <Select
                            onValueChange={field.onChange}
                            defaultValue={field.value}
                          >
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
                      name="accountName"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Account Name</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="Primary Account"
                              data-testid="input-account-name"
                              {...field}
                            />
                          </FormControl>
                          <FormDescription>
                            Friendly name for this AdMob account
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <div className="grid grid-cols-2 gap-4">
                      <FormField
                        control={form.control}
                        name="priority"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Priority</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                min="1"
                                data-testid="input-priority"
                                {...field}
                                onChange={(e) => field.onChange(parseInt(e.target.value))}
                              />
                            </FormControl>
                            <FormDescription>1 = highest</FormDescription>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="weight"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Weight (%)</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                min="0"
                                max="100"
                                data-testid="input-weight"
                                {...field}
                                onChange={(e) => field.onChange(parseInt(e.target.value))}
                              />
                            </FormControl>
                            <FormDescription>Traffic percentage</FormDescription>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>
                    <FormField
                      control={form.control}
                      name="status"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Status</FormLabel>
                          <Select
                            onValueChange={field.onChange}
                            defaultValue={field.value}
                          >
                            <FormControl>
                              <SelectTrigger data-testid="select-status">
                                <SelectValue placeholder="Select status" />
                              </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                              <SelectItem value="active">Active</SelectItem>
                              <SelectItem value="paused">Paused</SelectItem>
                              <SelectItem value="disabled">Disabled</SelectItem>
                            </SelectContent>
                          </Select>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </TabsContent>
                  <TabsContent value="adunits" className="space-y-4 mt-4">
                    <FormField
                      control={form.control}
                      name="bannerId"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Banner Ad Unit ID</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="ca-app-pub-XXXXX/XXXXX"
                              data-testid="input-banner-id"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="interstitialId"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Interstitial Ad Unit ID</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="ca-app-pub-XXXXX/XXXXX"
                              data-testid="input-interstitial-id"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="rewardedId"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Rewarded Ad Unit ID</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="ca-app-pub-XXXXX/XXXXX"
                              data-testid="input-rewarded-id"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="appOpenId"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>App Open Ad Unit ID</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="ca-app-pub-XXXXX/XXXXX"
                              data-testid="input-appopen-id"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="nativeId"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Native Ad Unit ID</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="ca-app-pub-XXXXX/XXXXX"
                              data-testid="input-native-id"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </TabsContent>
                </Tabs>
                <DialogFooter>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={handleCloseDialog}
                    data-testid="button-cancel"
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    disabled={createMutation.isPending}
                    data-testid="button-save-admob"
                  >
                    {createMutation.isPending ? "Saving..." : editingAccount ? "Update" : "Create"}
                  </Button>
                </DialogFooter>
              </form>
            </Form>
          </DialogContent>
        </Dialog>
      </div>

      {isLoading ? (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {[1, 2, 3].map((i) => (
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
      ) : accounts && accounts.length > 0 ? (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {accounts.map((account) => (
            <Card
              key={account.id}
              className="hover-elevate transition-all"
              data-testid={`card-admob-${account.id}`}
            >
              <CardHeader className="flex flex-row items-start justify-between gap-2 space-y-0 pb-3">
                <div className="flex items-center gap-3 flex-1 min-w-0">
                  <div className="p-2 rounded-md bg-chart-2/10 shrink-0">
                    <DollarSign className="h-5 w-5 text-chart-2" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <CardTitle className="text-base truncate" data-testid={`text-account-name-${account.id}`}>
                      {account.accountName}
                    </CardTitle>
                    <p className="text-xs text-muted-foreground truncate">
                      {getAppName(account.appId)}
                    </p>
                  </div>
                </div>
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button
                      variant="ghost"
                      size="icon"
                      className="h-8 w-8 shrink-0"
                      data-testid={`button-menu-${account.id}`}
                    >
                      <MoreVertical className="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem
                      onClick={() => handleEdit(account)}
                      data-testid={`button-edit-${account.id}`}
                    >
                      <Pencil className="h-4 w-4 mr-2" />
                      Edit
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      onClick={() => deleteMutation.mutate(account.id)}
                      className="text-destructive"
                      data-testid={`button-delete-${account.id}`}
                    >
                      <Trash2 className="h-4 w-4 mr-2" />
                      Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </CardHeader>
              <CardContent className="space-y-3">
                <div className="flex items-center justify-between text-sm">
                  <span className="text-muted-foreground">Priority:</span>
                  <span className="font-medium">{account.priority}</span>
                </div>
                <div className="flex items-center justify-between text-sm">
                  <span className="text-muted-foreground">Weight:</span>
                  <span className="font-medium">{account.weight}%</span>
                </div>
                <div className="flex items-center justify-between">
                  <Badge
                    variant={account.status === "active" ? "default" : "secondary"}
                    data-testid={`badge-status-${account.id}`}
                  >
                    {account.status}
                  </Badge>
                  <div className="flex gap-1">
                    {account.bannerId && <Badge variant="outline" className="text-xs">Banner</Badge>}
                    {account.interstitialId && <Badge variant="outline" className="text-xs">Int</Badge>}
                    {account.rewardedId && <Badge variant="outline" className="text-xs">Reward</Badge>}
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : (
        <Card>
          <CardContent className="flex flex-col items-center justify-center py-12">
            <div className="p-4 rounded-full bg-muted/50 mb-4">
              <DollarSign className="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 className="text-lg font-medium mb-2">No AdMob accounts yet</h3>
            <p className="text-sm text-muted-foreground mb-4 text-center max-w-sm">
              Add AdMob accounts to enable multi-account switching and revenue optimization.
            </p>
            <Button onClick={() => setIsDialogOpen(true)} data-testid="button-add-first-admob">
              <Plus className="h-4 w-4 mr-2" />
              Add Your First Account
            </Button>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
