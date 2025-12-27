import { useEffect, useState, useRef } from 'react';
import { Head, Link } from '@inertiajs/react';
import { Package, Eye, Clock } from 'lucide-react';
import { toast } from 'sonner';

import StorefrontLayout from '@/layouts/storefront-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ordersApi, Order } from '@/services/api';

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    collecting: 'bg-purple-100 text-purple-800',
    collected: 'bg-indigo-100 text-indigo-800',
    in_transit: 'bg-cyan-100 text-cyan-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

export default function CustomerOrders() {
    const [orders, setOrders] = useState<Order[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const hasFetched = useRef(false);

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;
        fetchOrders();
    }, []);

    const fetchOrders = async () => {
        try {
            const response = await ordersApi.myOrders({ per_page: 50 });
            setOrders(response.data.data);
        } catch (error) {
            toast.error('Failed to load orders');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <StorefrontLayout>
            <Head title="My Orders" />
            <div className="container mx-auto px-4 py-8">
                <h1 className="text-2xl font-bold mb-6">My Orders</h1>

                {isLoading ? (
                    <div className="text-center py-12 text-muted-foreground">Loading orders...</div>
                ) : orders.length === 0 ? (
                    <div className="text-center py-16">
                        <Package className="mx-auto h-16 w-16 text-muted-foreground mb-4" />
                        <h2 className="text-xl font-semibold mb-2">No orders yet</h2>
                        <p className="text-muted-foreground mb-6">
                            Start shopping and your orders will appear here.
                        </p>
                        <Link href="/shop">
                            <Button>Start Shopping</Button>
                        </Link>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {orders.map((order) => (
                            <div
                                key={order.id}
                                className="rounded-lg border bg-card p-6 hover:shadow-sm transition-shadow"
                            >
                                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <div className="flex items-center gap-3 mb-2">
                                            <span className="font-mono font-semibold">
                                                #{order.order_number}
                                            </span>
                                            <Badge className={statusColors[order.status] || 'bg-gray-100'}>
                                                {order.status.replace('_', ' ')}
                                            </Badge>
                                            {order.is_paid && (
                                                <Badge variant="outline" className="text-green-600 border-green-600">
                                                    Paid
                                                </Badge>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-4 text-sm text-muted-foreground">
                                            <span className="flex items-center gap-1">
                                                <Clock className="h-4 w-4" />
                                                {new Date(order.created_at).toLocaleDateString()}
                                            </span>
                                            <span>{order.items_count || order.items?.length || 0} items</span>
                                            {order.market && <span>from {order.market.name}</span>}
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-4">
                                        <div className="text-right">
                                            <div className="font-semibold text-lg">
                                                TZS {Number(order.total).toLocaleString()}
                                            </div>
                                        </div>
                                        <Link href={`/my-orders/${order.id}`}>
                                            <Button variant="outline" size="sm">
                                                <Eye className="mr-2 h-4 w-4" />
                                                View
                                            </Button>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </StorefrontLayout>
    );
}
