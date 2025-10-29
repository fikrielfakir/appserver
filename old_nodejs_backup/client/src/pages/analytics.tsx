import { useQuery } from "@tanstack/react-query";
import { BarChart3, TrendingUp, DollarSign, Eye } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Legend,
} from "recharts";
import type { App } from "@shared/schema";

export default function Analytics() {
  const { data: apps } = useQuery<App[]>({
    queryKey: ["/api/admin/apps"],
  });

  const revenueData = [
    { date: "Jan 1", account1: 2400, account2: 1800, account3: 900 },
    { date: "Jan 2", account1: 2800, account2: 2100, account3: 1100 },
    { date: "Jan 3", account1: 2600, account2: 1900, account3: 1000 },
    { date: "Jan 4", account1: 3200, account2: 2400, account3: 1300 },
    { date: "Jan 5", account1: 3600, account2: 2800, account3: 1500 },
    { date: "Jan 6", account1: 3400, account2: 2600, account3: 1400 },
    { date: "Jan 7", account1: 3800, account2: 3000, account3: 1600 },
  ];

  const impressionsData = [
    { name: "Banner", impressions: 45000, clicks: 2200 },
    { name: "Interstitial", impressions: 28000, clicks: 3400 },
    { name: "Rewarded", impressions: 18000, clicks: 4500 },
    { name: "Native", impressions: 12000, clicks: 1200 },
  ];

  const stats = [
    {
      title: "Total Revenue",
      value: "$12,450",
      change: "+18.2%",
      icon: DollarSign,
      color: "text-chart-2",
      bgColor: "bg-chart-2/10",
    },
    {
      title: "Impressions",
      value: "1.2M",
      change: "+12.5%",
      icon: Eye,
      color: "text-chart-1",
      bgColor: "bg-chart-1/10",
    },
    {
      title: "Fill Rate",
      value: "94.8%",
      change: "+2.1%",
      icon: BarChart3,
      color: "text-chart-3",
      bgColor: "bg-chart-3/10",
    },
    {
      title: "eCPM",
      value: "$8.45",
      change: "+5.3%",
      icon: TrendingUp,
      color: "text-chart-4",
      bgColor: "bg-chart-4/10",
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-medium mb-2">Analytics</h1>
          <p className="text-muted-foreground">
            Monitor AdMob performance and notification metrics
          </p>
        </div>
        <div className="flex items-center gap-3">
          <Select defaultValue="7days">
            <SelectTrigger className="w-[180px]" data-testid="select-timeframe">
              <SelectValue placeholder="Select timeframe" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="24hours">Last 24 Hours</SelectItem>
              <SelectItem value="7days">Last 7 Days</SelectItem>
              <SelectItem value="30days">Last 30 Days</SelectItem>
              <SelectItem value="90days">Last 90 Days</SelectItem>
            </SelectContent>
          </Select>
          <Select defaultValue="all">
            <SelectTrigger className="w-[180px]" data-testid="select-app-filter">
              <SelectValue placeholder="Select app" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Apps</SelectItem>
              {apps?.map((app) => (
                <SelectItem key={app.id} value={app.id}>
                  {app.appName}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
      </div>

      {/* Key Metrics */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {stats.map((stat) => (
          <Card key={stat.title} className="hover-elevate transition-all">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">{stat.title}</CardTitle>
              <div className={`p-2 rounded-md ${stat.bgColor}`}>
                <stat.icon className={`h-4 w-4 ${stat.color}`} />
              </div>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-medium" data-testid={`stat-${stat.title.toLowerCase().replace(/\s+/g, '-')}`}>
                {stat.value}
              </div>
              <p className="text-xs text-muted-foreground mt-1">
                <span className="text-chart-2">{stat.change}</span> from last period
              </p>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Revenue Trend */}
      <Card>
        <CardHeader>
          <CardTitle>Revenue by Account</CardTitle>
          <CardDescription>
            Daily revenue breakdown across AdMob accounts
          </CardDescription>
        </CardHeader>
        <CardContent>
          <ResponsiveContainer width="100%" height={350}>
            <LineChart data={revenueData}>
              <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
              <XAxis
                dataKey="date"
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
                formatter={(value: number) => [`$${value}`, ""]}
              />
              <Legend />
              <Line
                type="monotone"
                dataKey="account1"
                stroke="hsl(var(--chart-1))"
                strokeWidth={2}
                name="Primary Account"
                dot={{ fill: "hsl(var(--chart-1))", r: 3 }}
              />
              <Line
                type="monotone"
                dataKey="account2"
                stroke="hsl(var(--chart-2))"
                strokeWidth={2}
                name="Backup Account"
                dot={{ fill: "hsl(var(--chart-2))", r: 3 }}
              />
              <Line
                type="monotone"
                dataKey="account3"
                stroke="hsl(var(--chart-3))"
                strokeWidth={2}
                name="Test Account"
                dot={{ fill: "hsl(var(--chart-3))", r: 3 }}
              />
            </LineChart>
          </ResponsiveContainer>
        </CardContent>
      </Card>

      {/* Ad Performance */}
      <div className="grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Ad Unit Performance</CardTitle>
            <CardDescription>
              Impressions and clicks by ad format
            </CardDescription>
          </CardHeader>
          <CardContent>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={impressionsData}>
                <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                <XAxis
                  dataKey="name"
                  stroke="hsl(var(--muted-foreground))"
                  fontSize={12}
                />
                <YAxis
                  stroke="hsl(var(--muted-foreground))"
                  fontSize={12}
                />
                <Tooltip
                  contentStyle={{
                    backgroundColor: "hsl(var(--popover))",
                    border: "1px solid hsl(var(--border))",
                    borderRadius: "6px",
                  }}
                />
                <Legend />
                <Bar
                  dataKey="impressions"
                  fill="hsl(var(--chart-1))"
                  radius={[4, 4, 0, 0]}
                  name="Impressions"
                />
                <Bar
                  dataKey="clicks"
                  fill="hsl(var(--chart-2))"
                  radius={[4, 4, 0, 0]}
                  name="Clicks"
                />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Notification Analytics</CardTitle>
            <CardDescription>Recent notification performance</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {[
                { title: "Special Offer", delivered: 15420, opened: 8234, rate: "53.4%" },
                { title: "New Features", delivered: 12350, opened: 5890, rate: "47.7%" },
                { title: "Update Available", delivered: 9870, opened: 4523, rate: "45.8%" },
              ].map((notif, index) => (
                <div key={index} className="flex items-center justify-between p-3 rounded-lg border border-border">
                  <div className="flex-1">
                    <p className="text-sm font-medium">{notif.title}</p>
                    <p className="text-xs text-muted-foreground">
                      {notif.delivered.toLocaleString()} delivered • {notif.opened.toLocaleString()} opened
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-medium text-chart-2">{notif.rate}</p>
                    <p className="text-xs text-muted-foreground">Open rate</p>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Geographic Distribution */}
      <Card>
        <CardHeader>
          <CardTitle>Top Countries by Revenue</CardTitle>
          <CardDescription>Geographic distribution of ad revenue</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {[
              { country: "United States", revenue: 4520, percentage: 38 },
              { country: "United Kingdom", revenue: 2340, percentage: 20 },
              { country: "Canada", revenue: 1890, percentage: 16 },
              { country: "Australia", revenue: 1450, percentage: 12 },
              { country: "Germany", revenue: 1250, percentage: 10 },
              { country: "Other", revenue: 500, percentage: 4 },
            ].map((item, index) => (
              <div key={index} className="flex items-center gap-4">
                <div className="w-32 text-sm font-medium truncate">{item.country}</div>
                <div className="flex-1">
                  <div className="h-2 bg-muted rounded-full overflow-hidden">
                    <div
                      className="h-full bg-primary rounded-full"
                      style={{ width: `${item.percentage}%` }}
                    />
                  </div>
                </div>
                <div className="w-20 text-right text-sm font-medium">
                  ${item.revenue.toLocaleString()}
                </div>
                <div className="w-12 text-right text-xs text-muted-foreground">
                  {item.percentage}%
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
