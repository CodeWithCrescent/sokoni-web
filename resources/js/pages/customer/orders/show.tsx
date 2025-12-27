import { useEffect, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft, MapPin, Phone, Clock, Package } from 'lucide-react';
import { toast } from 'sonner';

import StorefrontLayout from '@/layouts/storefront-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ordersApi, Order } from '@/services/api';

interface Props {
    orderId: number;
}

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    collecting: 'bg-purple-100 text-purple-800',
    collected: 'bg-indigo-100 text-indigo-800',
    in_transit: 'bg-cyan-100 text-cyan-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const statusSteps = ['pending', 'confirmed', 'collecting', 'collected', 'in_transit', 'delivered'];

export default function OrderShow({ orderId }: Props) {
    const [order, setOrder] = useState<Order | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetchOrder();
    }, [orderId]);

    const fetchOrder = async () => {
        try {
            const response = await ordersApi.get(orderId);
            setOrder(response.data.data);
        } catch (error) {
            toast.error('Failed to load order');
            router.visit('/my-orders');
        } finally {
            setIsLoading(false);
        }
    };

    if (isLoading) {
        return (
            <StorefrontLayout>
                <Head title="Order Details" />
                <div className="container mx-auto px-4 py-8">
                    <div className="text-center py-12 text-muted-foreground">Loading order...</div>
                </div>
            </StorefrontLayout>
        );
    }

    if (!order) return null;

    const currentStepIndex = statusSteps.indexOf(order.status);

    return (
        <StorefrontLayout>
            <Head title={`Order #${order.order_number}`} />
            <div className="container mx-auto px-4 py-8">
                <Link href="/my-orders" className="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-6">
                    <ArrowLeft className="mr-2 h-4 w-4" />
                    Back to Orders
                </Link>

                <div className="grid lg:grid-cols-3 gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Order Header */}
                        <div className="rounded-lg border bg-card p-6">
                            <div className="flex items-center justify-between mb-4">
                                <div>
                                    <h1 className="text-xl font-bold">Order #{order.order_number}</h1>
                                    <p className="text-sm text-muted-foreground">
                                        Placed on {new Date(order.created_at).toLocaleDateString()}
                                    </p>
                                </div>
                                <Badge className={statusColors[order.status] || 'bg-gray-100'} >
                                    {order.status.replace('_', ' ')}
                                </Badge>
                            </div>

                            {/* Status Progress */}
                            {order.status !== 'cancelled' && (
                                <div className="mt-6">
                                    <div className="flex items-center justify-between">
                                        {statusSteps.map((step, index) => (
                                            <div key={step} className="flex flex-col items-center flex-1">
                                                <div
                                                    className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium ${
                                                        index <= currentStepIndex
                                                            ? 'bg-primary text-primary-foreground'
                                                            : 'bg-muted text-muted-foreground'
                                                    }`}
                                                >
                                                    {index + 1}
                                                </div>
                                                <span className="text-xs mt-1 text-center capitalize hidden sm:block">
                                                    {step.replace('_', ' ')}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Order Items */}
                        <div className="rounded-lg border bg-card p-6">
                            <h2 className="font-semibold mb-4 flex items-center gap-2">
                                <Package className="h-5 w-5" />
                                Order Items
                            </h2>
                            <div className="space-y-4">
                                {order.items?.map((item) => (
                                    <div key={item.id} className="flex items-center justify-between py-3 border-b last:border-0">
                                        <div>
                                            <div className="font-medium">{item.product_name}</div>
                                            <div className="text-sm text-muted-foreground">
                                                {item.quantity} x TZS {Number(item.unit_price).toLocaleString()} / {item.unit_name}
                                            </div>
                                        </div>
                                        <div className="font-medium">
                                            TZS {Number(item.total_price).toLocaleString()}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Delivery Info */}
                        <div className="rounded-lg border bg-card p-6">
                            <h2 className="font-semibold mb-4">Delivery Details</h2>
                            <div className="space-y-3 text-sm">
                                <div className="flex items-start gap-3">
                                    <MapPin className="h-4 w-4 mt-0.5 text-muted-foreground" />
                                    <span>{order.delivery_address}</span>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Phone className="h-4 w-4 text-muted-foreground" />
                                    <span>{order.delivery_phone}</span>
                                </div>
                                {order.delivery_instructions && (
                                    <div className="pt-2 border-t">
                                        <span className="text-muted-foreground">Instructions: </span>
                                        {order.delivery_instructions}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Order Summary */}
                        <div className="rounded-lg border bg-card p-6">
                            <h2 className="font-semibold mb-4">Order Summary</h2>
                            <div className="space-y-2 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Subtotal</span>
                                    <span>TZS {Number(order.subtotal).toLocaleString()}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Delivery Fee</span>
                                    <span>TZS {Number(order.delivery_fee).toLocaleString()}</span>
                                </div>
                                {order.discount > 0 && (
                                    <div className="flex justify-between text-green-600">
                                        <span>Discount</span>
                                        <span>-TZS {Number(order.discount).toLocaleString()}</span>
                                    </div>
                                )}
                                <div className="flex justify-between pt-2 border-t font-semibold text-base">
                                    <span>Total</span>
                                    <span>TZS {Number(order.total).toLocaleString()}</span>
                                </div>
                            </div>
                            <div className="mt-4 pt-4 border-t">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Payment Status</span>
                                    <Badge variant={order.is_paid ? 'default' : 'secondary'}>
                                        {order.is_paid ? 'Paid' : 'Unpaid'}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        {/* Actions */}
                        {order.can_be_cancelled && (
                            <Button variant="outline" className="w-full text-destructive hover:text-destructive">
                                Cancel Order
                            </Button>
                        )}
                    </div>
                </div>
            </div>
        </StorefrontLayout>
    );
}
