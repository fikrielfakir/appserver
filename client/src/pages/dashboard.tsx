import { useQuery } from "@tanstack/react-query";
import {
  Smartphone,
  DollarSign,
  Bell,
  Users,
  TrendingUp,
  Activity,
} from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
} from "recharts";

interface DashboardStats {
  totalApps: number;
  totalAdmobAccounts: number;
  totalNotifications: number;
  totalDevices: number;
  recentRevenue: number;
  revenueChange: number;
}

const COLORS = ["hsl(var(--chart-1))", "hsl(var(--chart-2))", "hsl(var(--chart-3))", "hsl(var(--chart-4))"];

export default function Dashboard() {
  const { data: stats, isLoading } = useQuery<DashboardStats>({
    queryKey: ["/api/admin/dashboard/stats"],
  });

  const revenueData = [
    { name: "Mon", revenue: 4200 },
    { name: "Tue", revenue: 5100 },
    { name: "Wed", revenue: 4800 },
    { name: "Thu", revenue: 6200 },
    { name: "Fri", revenue: 7100 },
    { name: "Sat", revenue: 6800 },
    { name: "Sun", revenue: 5900 },
  ];

  const accountDistribution = [
    { name: "Primary", value: 45 },
    { name: "Backup", value: 30 },
    { name: "Testing", value: 15 },
    { name: "Geographic", value: 10 },
  ];

  const statCards = [
    {
      title: "Total Apps",
      value: stats?.totalApps || 0,
      icon: Smartphone,
      color: "text-chart-1",
      bgColor: "bg-chart-1/10",
      testId: "stat-apps",
    },
    {
      title: "AdMob Accounts",
      value: stats?.totalAdmobAccounts || 0,
      icon: DollarSign,
      color: "text-chart-2",
      bgColor: "bg-chart-2/10",
      testId: "stat-admob",
    },
    {
      title: "Notifications",
      value: stats?.totalNotifications || 0,
      icon: Bell,
      color: "text-chart-3",
      bgColor: "bg-chart-3/10",
      testId: "stat-notifications",
    },
    {
      title: "Active Devices",
      value: stats?.totalDevices || 0,
      icon: Users,
      color: "text-chart-4",
      bgColor: "bg-chart-4/10",
      testId: "stat-devices",
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-medium mb-2">Dashboard</h1>
        <p className="text-muted-foreground">
          Overview of your Android platform control system
        </p>
      </div>

      {/* Stats Grid */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {statCards.map((stat) => (
          <Card key={stat.title} className="hover-elevate transition-all">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">
                {stat.title}
              </CardTitle>
              <div className={`p-2 rounded-md ${stat.bgColor}`}>
                <stat.icon className={`h-4 w-4 ${stat.color}`} />
              </div>
            </CardHeader>
            <CardContent>
              {isLoading ? (
                <Skeleton className="h-8 w-20" />
              ) : (
                <div className="text-2xl font-medium" data-testid={stat.testId}>
                  {stat.value.toLocaleString()}
                </div>
              )}
              <p className="text-xs text-muted-foreground mt-1">
                {stat.title === "Total Apps" && "Managed applications"}
                {stat.title === "AdMob Accounts" && "Active accounts"}
                {stat.title === "Notifications" && "Total sent"}
                {stat.title === "Active Devices" && "Registered devices"}
              </p>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        {/* Revenue Chart */}
        <Card>
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle>Revenue Trend</CardTitle>
              <TrendingUp className="h-5 w-5 text-chart-2" />
            </div>
            <p className="text-sm text-muted-foreground">
              Last 7 days performance
            </p>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={250}>
              <LineChart data={revenueData}>
                <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                <XAxis
                  dataKey="name"
                  stroke="hsl(var(--muted-foreground))"
                  fontSize={12}
                />
                <YAxis
                  stroke="hsl(var(--muted-foreground))"
                  fontSize={12}
                  tickFormatter={(value) => `$${value}`}
                />
                <Tooltip
                  contentStyle={{
                    backgroundColor: "hsl(var(--popover))",
                    border: "1px solid hsl(var(--border))",
                    borderRadius: "6px",
                  }}
                  formatter={(value: number) => [`$${value}`, "Revenue"]}
                />
                <Line
                  type="monotone"
                  dataKey="revenue"
                  stroke="hsl(var(--chart-2))"
                  strokeWidth={2}
                  dot={{ fill: "hsl(var(--chart-2))", r: 4 }}
                />
              </LineChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        {/* Account Distribution */}
        <Card>
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle>Account Distribution</CardTitle>
              <Activity className="h-5 w-5 text-chart-1" />
            </div>
            <p className="text-sm text-muted-foreground">
              Traffic split across accounts
            </p>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={250}>
              <PieChart>
                <Pie
                  data={accountDistribution}
                  cx="50%"
                  cy="50%"
                  labelLine={false}
                  label={({ name, value }) => `${name}: ${value}%`}
                  outerRadius={80}
                  fill="hsl(var(--chart-1))"
                  dataKey="value"
                >
                  {accountDistribution.map((entry, index) => (
                    <Cell
                      key={`cell-${index}`}
                      fill={COLORS[index % COLORS.length]}
                    />
                  ))}
                </Pie>
                <Tooltip
                  contentStyle={{
                    backgroundColor: "hsl(var(--popover))",
                    border: "1px solid hsl(var(--border))",
                    borderRadius: "6px",
                  }}
                  formatter={(value: number) => [`${value}%`, "Traffic"]}
                />
              </PieChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>

      {/* Quick Actions */}
      <Card>
        <CardHeader>
          <CardTitle>Quick Actions</CardTitle>
          <p className="text-sm text-muted-foreground">
            Common management tasks
          </p>
        </CardHeader>
        <CardContent>
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {[
              { label: "Add New App", href: "/apps", icon: Smartphone },
              { label: "Create AdMob Account", href: "/admob", icon: DollarSign },
              { label: "Send Notification", href: "/notifications", icon: Bell },
              { label: "View Analytics", href: "/analytics", icon: Activity },
            ].map((action) => (
              <a
                key={action.label}
                href={action.href}
                className="flex items-center gap-3 p-3 rounded-md border border-border hover-elevate active-elevate-2 transition-all"
                data-testid={`link-${action.label.toLowerCase().replace(/\s+/g, '-')}`}
              >
                <div className="p-2 rounded-md bg-primary/10">
                  <action.icon className="h-4 w-4 text-primary" />
                </div>
                <span className="text-sm font-medium">{action.label}</span>
              </a>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
