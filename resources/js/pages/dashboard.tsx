import { useEffect, useState, useRef } from 'react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { ShoppingCart, DollarSign, Users, Package, TrendingUp, Store } from 'lucide-react';
import { StatCard } from '@/components/ui/stat-card';
import { analyticsApi, DashboardStats, TopProduct, TopMarket } from '@/services/api';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard() {
    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [topProducts, setTopProducts] = useState<TopProduct[]>([]);
    const [topMarkets, setTopMarkets] = useState<TopMarket[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const hasFetched = useRef(false);

    const fetchData = async () => {
        try {
            const [dashboardRes, productsRes, marketsRes] = await Promise.all([
                analyticsApi.dashboard(),
                analyticsApi.topProducts(5),
                analyticsApi.topMarkets(5),
            ]);
            setStats(dashboardRes.data.data);
            setTopProducts(productsRes.data.data);
            setTopMarkets(marketsRes.data.data);
        } catch (error) {
            toast.error('Failed to load dashboard data');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;
        fetchData();
    }, []);

    const formatCurrency = (amount: number) => {
        return `TZS ${amount.toLocaleString()}`;
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6 p-6">
                <h1 className="text-2xl font-semibold">Dashboard</h1>

                {isLoading ? (
                    <div className="text-muted-foreground">Loading...</div>
                ) : stats ? (
                    <>
                        {/* Stats Grid */}
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <StatCard
                                title="Total Revenue"
                                value={formatCurrency(stats.revenue.total)}
                                description={`${formatCurrency(stats.revenue.this_month)} this month`}
                                icon={DollarSign}
                                trend={{
                                    value: stats.revenue.growth_percent,
                                    isPositive: stats.revenue.growth_percent >= 0,
                                }}
                            />
                            <StatCard
                                title="Total Orders"
                                value={stats.orders.total}
                                description={`${stats.orders.today} today, ${stats.orders.pending} pending`}
                                icon={ShoppingCart}
                            />
                            <StatCard
                                title="Total Users"
                                value={stats.users.total}
                                description={`${stats.users.new_this_month} new this month`}
                                icon={Users}
                            />
                            <StatCard
                                title="Active Markets"
                                value={stats.catalog.active_markets}
                                description={`${stats.catalog.total_products} products`}
                                icon={Store}
                            />
                        </div>

                        {/* Order Status */}
                        <div className="rounded-lg border bg-card p-6">
                            <h2 className="mb-4 text-lg font-semibold">Orders by Status</h2>
                            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                {Object.entries(stats.orders.by_status).map(([status, count]) => (
                                    <div key={status} className="flex items-center justify-between rounded-md bg-muted/50 px-4 py-3">
                                        <span className="text-sm capitalize">{status.replace('_', ' ')}</span>
                                        <span className="font-semibold">{count}</span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Top Products & Markets */}
                        <div className="grid gap-6 lg:grid-cols-2">
                            <div className="rounded-lg border bg-card p-6">
                                <h2 className="mb-4 text-lg font-semibold flex items-center gap-2">
                                    <Package className="h-5 w-5" />
                                    Top Products
                                </h2>
                                {topProducts.length > 0 ? (
                                    <div className="space-y-3">
                                        {topProducts.map((product, index) => (
                                            <div key={product.id} className="flex items-center justify-between">
                                                <div className="flex items-center gap-3">
                                                    <span className="flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-xs font-medium">
                                                        {index + 1}
                                                    </span>
                                                    <span className="text-sm">{product.name}</span>
                                                </div>
                                                <span className="text-sm font-medium">{formatCurrency(product.total_revenue)}</span>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-sm text-muted-foreground">No data available</p>
                                )}
                            </div>

                            <div className="rounded-lg border bg-card p-6">
                                <h2 className="mb-4 text-lg font-semibold flex items-center gap-2">
                                    <TrendingUp className="h-5 w-5" />
                                    Top Markets
                                </h2>
                                {topMarkets.length > 0 ? (
                                    <div className="space-y-3">
                                        {topMarkets.map((market, index) => (
                                            <div key={market.id} className="flex items-center justify-between">
                                                <div className="flex items-center gap-3">
                                                    <span className="flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-xs font-medium">
                                                        {index + 1}
                                                    </span>
                                                    <div>
                                                        <span className="text-sm">{market.name}</span>
                                                        <span className="ml-2 text-xs text-muted-foreground">
                                                            ({market.order_count} orders)
                                                        </span>
                                                    </div>
                                                </div>
                                                <span className="text-sm font-medium">{formatCurrency(market.total_revenue)}</span>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-sm text-muted-foreground">No data available</p>
                                )}
                            </div>
                        </div>
                    </>
                ) : (
                    <div className="text-muted-foreground">No data available</div>
                )}
            </div>
        </AppLayout>
    );
}
