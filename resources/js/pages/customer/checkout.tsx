import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { MapPin, CreditCard, Smartphone, Banknote } from 'lucide-react';
import { toast } from 'sonner';

import StorefrontLayout from '@/layouts/storefront-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

interface CartItem {
    product_id: number;
    name: string;
    unit: string;
    price: number;
    quantity: number;
}

export default function Checkout() {
    const [items, setItems] = useState<CartItem[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [formData, setFormData] = useState({
        delivery_address: '',
        delivery_phone: '',
        delivery_instructions: '',
        payment_method: 'mpesa',
        mpesa_phone: '',
    });

    useEffect(() => {
        const cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
        if (cart.length === 0) {
            router.visit('/cart');
            return;
        }
        setItems(cart);
    }, []);

    const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const deliveryFee = 3000;
    const total = subtotal + deliveryFee;

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            // In a real implementation, this would call the order API
            toast.success('Order placed successfully!');
            localStorage.removeItem('guest_cart');
            router.visit('/my-orders');
        } catch (error) {
            toast.error('Failed to place order');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <StorefrontLayout>
            <Head title="Checkout" />
            <div className="container mx-auto px-4 py-8">
                <h1 className="text-2xl font-bold mb-8">Checkout</h1>

                <form onSubmit={handleSubmit}>
                    <div className="grid lg:grid-cols-3 gap-8">
                        {/* Main Form */}
                        <div className="lg:col-span-2 space-y-6">
                            {/* Delivery Address */}
                            <div className="rounded-lg border bg-card p-6">
                                <h2 className="font-semibold mb-4 flex items-center gap-2">
                                    <MapPin className="h-5 w-5" />
                                    Delivery Address
                                </h2>
                                <div className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="address">Address</Label>
                                        <Textarea
                                            id="address"
                                            value={formData.delivery_address}
                                            onChange={(e) => setFormData({ ...formData, delivery_address: e.target.value })}
                                            placeholder="Enter your full delivery address"
                                            required
                                            rows={2}
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="phone">Phone Number</Label>
                                        <Input
                                            id="phone"
                                            value={formData.delivery_phone}
                                            onChange={(e) => setFormData({ ...formData, delivery_phone: e.target.value })}
                                            placeholder="+255 xxx xxx xxx"
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="instructions">Delivery Instructions (Optional)</Label>
                                        <Textarea
                                            id="instructions"
                                            value={formData.delivery_instructions}
                                            onChange={(e) => setFormData({ ...formData, delivery_instructions: e.target.value })}
                                            placeholder="Any special instructions for delivery"
                                            rows={2}
                                        />
                                    </div>
                                </div>
                            </div>

                            {/* Payment Method */}
                            <div className="rounded-lg border bg-card p-6">
                                <h2 className="font-semibold mb-4 flex items-center gap-2">
                                    <CreditCard className="h-5 w-5" />
                                    Payment Method
                                </h2>
                                <RadioGroup
                                    value={formData.payment_method}
                                    onValueChange={(value) => setFormData({ ...formData, payment_method: value })}
                                    className="space-y-3"
                                >
                                    <div className="flex items-center space-x-3 rounded-lg border p-4">
                                        <RadioGroupItem value="mpesa" id="mpesa" />
                                        <Label htmlFor="mpesa" className="flex items-center gap-2 cursor-pointer flex-1">
                                            <Smartphone className="h-5 w-5 text-green-600" />
                                            <div>
                                                <div className="font-medium">M-Pesa</div>
                                                <div className="text-sm text-muted-foreground">Pay via mobile money</div>
                                            </div>
                                        </Label>
                                    </div>
                                    <div className="flex items-center space-x-3 rounded-lg border p-4">
                                        <RadioGroupItem value="cash" id="cash" />
                                        <Label htmlFor="cash" className="flex items-center gap-2 cursor-pointer flex-1">
                                            <Banknote className="h-5 w-5 text-amber-600" />
                                            <div>
                                                <div className="font-medium">Cash on Delivery</div>
                                                <div className="text-sm text-muted-foreground">Pay when you receive</div>
                                            </div>
                                        </Label>
                                    </div>
                                </RadioGroup>

                                {formData.payment_method === 'mpesa' && (
                                    <div className="mt-4 space-y-2">
                                        <Label htmlFor="mpesa_phone">M-Pesa Phone Number</Label>
                                        <Input
                                            id="mpesa_phone"
                                            value={formData.mpesa_phone}
                                            onChange={(e) => setFormData({ ...formData, mpesa_phone: e.target.value })}
                                            placeholder="0712 xxx xxx"
                                        />
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Order Summary Sidebar */}
                        <div className="lg:col-span-1">
                            <div className="rounded-lg border bg-card p-6 sticky top-20">
                                <h2 className="font-semibold mb-4">Order Summary</h2>
                                
                                <div className="space-y-3 mb-4 max-h-48 overflow-y-auto">
                                    {items.map((item) => (
                                        <div key={item.product_id} className="flex justify-between text-sm">
                                            <span className="text-muted-foreground">
                                                {item.name} x {item.quantity}
                                            </span>
                                            <span>TZS {(item.price * item.quantity).toLocaleString()}</span>
                                        </div>
                                    ))}
                                </div>

                                <div className="border-t pt-4 space-y-2 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Subtotal</span>
                                        <span>TZS {subtotal.toLocaleString()}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Delivery Fee</span>
                                        <span>TZS {deliveryFee.toLocaleString()}</span>
                                    </div>
                                    <div className="flex justify-between pt-2 border-t font-semibold text-base">
                                        <span>Total</span>
                                        <span>TZS {total.toLocaleString()}</span>
                                    </div>
                                </div>

                                <Button type="submit" className="w-full mt-6" disabled={isLoading}>
                                    {isLoading ? 'Placing Order...' : 'Place Order'}
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </StorefrontLayout>
    );
}
