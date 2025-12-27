import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { Trash2, Plus, Minus, ShoppingBag, ArrowRight } from 'lucide-react';
import StorefrontLayout from '@/layouts/storefront-layout';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';

interface CartItem {
    product_id: number;
    name: string;
    unit: string;
    price: number;
    quantity: number;
}

export default function Cart() {
    const [items, setItems] = useState<CartItem[]>([]);

    useEffect(() => {
        loadCart();
        window.addEventListener('cart-updated', loadCart);
        return () => window.removeEventListener('cart-updated', loadCart);
    }, []);

    const loadCart = () => {
        const cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
        setItems(cart);
    };

    const updateQuantity = (productId: number, delta: number) => {
        const cart = items.map((item) => {
            if (item.product_id === productId) {
                const newQuantity = Math.max(1, item.quantity + delta);
                return { ...item, quantity: newQuantity };
            }
            return item;
        });
        setItems(cart);
        localStorage.setItem('guest_cart', JSON.stringify(cart));
    };

    const removeItem = (productId: number) => {
        const cart = items.filter((item) => item.product_id !== productId);
        setItems(cart);
        localStorage.setItem('guest_cart', JSON.stringify(cart));
        toast.success('Item removed from cart');
    };

    const clearCart = () => {
        setItems([]);
        localStorage.removeItem('guest_cart');
        toast.success('Cart cleared');
    };

    const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);

    const handleCheckout = () => {
        if (items.length === 0) {
            toast.error('Your cart is empty');
            return;
        }
        router.visit('/checkout');
    };

    return (
        <StorefrontLayout>
            <Head title="Shopping Cart" />

            <div className="container mx-auto px-4 py-8">
                <h1 className="text-2xl font-bold mb-8">Shopping Cart</h1>

                {items.length === 0 ? (
                    <div className="text-center py-16">
                        <ShoppingBag className="mx-auto h-16 w-16 text-muted-foreground mb-4" />
                        <h2 className="text-xl font-semibold mb-2">Your cart is empty</h2>
                        <p className="text-muted-foreground mb-6">
                            Looks like you haven't added anything to your cart yet.
                        </p>
                        <Link href="/shop">
                            <Button>
                                Start Shopping
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Button>
                        </Link>
                    </div>
                ) : (
                    <div className="grid lg:grid-cols-3 gap-8">
                        {/* Cart Items */}
                        <div className="lg:col-span-2 space-y-4">
                            {items.map((item) => (
                                <div
                                    key={item.product_id}
                                    className="flex items-center gap-4 p-4 rounded-lg border bg-card"
                                >
                                    <div className="h-20 w-20 rounded-md bg-muted flex items-center justify-center text-2xl">
                                        🥬
                                    </div>
                                    <div className="flex-1">
                                        <h3 className="font-medium">{item.name}</h3>
                                        <p className="text-sm text-muted-foreground">
                                            TZS {item.price.toLocaleString()} / {item.unit}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            className="h-8 w-8"
                                            onClick={() => updateQuantity(item.product_id, -1)}
                                        >
                                            <Minus className="h-4 w-4" />
                                        </Button>
                                        <span className="w-8 text-center font-medium">
                                            {item.quantity}
                                        </span>
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            className="h-8 w-8"
                                            onClick={() => updateQuantity(item.product_id, 1)}
                                        >
                                            <Plus className="h-4 w-4" />
                                        </Button>
                                    </div>
                                    <div className="text-right">
                                        <p className="font-semibold">
                                            TZS {(item.price * item.quantity).toLocaleString()}
                                        </p>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            className="text-destructive hover:text-destructive"
                                            onClick={() => removeItem(item.product_id)}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            ))}

                            <div className="flex justify-end">
                                <Button variant="outline" size="sm" onClick={clearCart}>
                                    Clear Cart
                                </Button>
                            </div>
                        </div>

                        {/* Order Summary */}
                        <div className="lg:col-span-1">
                            <div className="rounded-lg border bg-card p-6 sticky top-20">
                                <h2 className="font-semibold text-lg mb-4">Order Summary</h2>
                                <div className="space-y-3 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Subtotal</span>
                                        <span>TZS {subtotal.toLocaleString()}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Delivery</span>
                                        <span className="text-muted-foreground">Calculated at checkout</span>
                                    </div>
                                    <div className="border-t pt-3 flex justify-between font-semibold text-base">
                                        <span>Total</span>
                                        <span>TZS {subtotal.toLocaleString()}</span>
                                    </div>
                                </div>
                                <Button className="w-full mt-6" onClick={handleCheckout}>
                                    Proceed to Checkout
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </Button>
                                <p className="text-xs text-muted-foreground text-center mt-4">
                                    You'll need to login or create an account to complete your order.
                                </p>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </StorefrontLayout>
    );
}
