import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { ArrowRight, Truck, Shield, Clock } from 'lucide-react';
import StorefrontLayout from '@/layouts/storefront-layout';
import { Button } from '@/components/ui/button';
import { apiClient } from '@/services/api';

interface Category {
    id: number;
    name: string;
    slug: string;
    products_count: number;
}

interface Market {
    id: number;
    name: string;
    address: string;
    is_open: boolean;
}

export default function Home() {
    const [categories, setCategories] = useState<Category[]>([]);
    const [markets, setMarkets] = useState<Market[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [catRes, marketRes] = await Promise.all([
                    apiClient.get('/browse/categories'),
                    apiClient.get('/browse/markets', { params: { limit: 4 } }),
                ]);
                setCategories(catRes.data.data || []);
                setMarkets(marketRes.data.data || []);
            } catch (error) {
                console.error('Failed to load data');
            } finally {
                setIsLoading(false);
            }
        };
        fetchData();
    }, []);

    return (
        <StorefrontLayout>
            <Head title="Home - Fresh from the Market" />

            {/* Hero Section */}
            <section className="relative bg-gradient-to-br from-primary/10 via-background to-background">
                <div className="container mx-auto px-4 py-20 md:py-32">
                    <div className="max-w-2xl">
                        <h1 className="text-4xl md:text-6xl font-bold tracking-tight mb-6">
                            Fresh Produce,<br />
                            <span className="text-primary">Delivered Daily</span>
                        </h1>
                        <p className="text-lg text-muted-foreground mb-8">
                            Order fresh fruits, vegetables, and more from local markets in Dar es Salaam. 
                            We bring the market to your doorstep.
                        </p>
                        <div className="flex flex-wrap gap-4">
                            <Link href="/shop">
                                <Button size="lg">
                                    Start Shopping
                                    <ArrowRight className="ml-2 h-5 w-5" />
                                </Button>
                            </Link>
                            <Link href="/markets">
                                <Button variant="outline" size="lg">
                                    View Markets
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            {/* Features */}
            <section className="py-16 border-b">
                <div className="container mx-auto px-4">
                    <div className="grid md:grid-cols-3 gap-8">
                        <div className="flex items-start gap-4">
                            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Truck className="h-6 w-6 text-primary" />
                            </div>
                            <div>
                                <h3 className="font-semibold mb-1">Fast Delivery</h3>
                                <p className="text-sm text-muted-foreground">
                                    Same-day delivery across Dar es Salaam
                                </p>
                            </div>
                        </div>
                        <div className="flex items-start gap-4">
                            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Shield className="h-6 w-6 text-primary" />
                            </div>
                            <div>
                                <h3 className="font-semibold mb-1">Quality Guaranteed</h3>
                                <p className="text-sm text-muted-foreground">
                                    Fresh produce directly from trusted markets
                                </p>
                            </div>
                        </div>
                        <div className="flex items-start gap-4">
                            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Clock className="h-6 w-6 text-primary" />
                            </div>
                            <div>
                                <h3 className="font-semibold mb-1">Save Time</h3>
                                <p className="text-sm text-muted-foreground">
                                    No more queues - order from anywhere
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Categories */}
            <section className="py-16">
                <div className="container mx-auto px-4">
                    <div className="flex items-center justify-between mb-8">
                        <h2 className="text-2xl font-bold">Shop by Category</h2>
                        <Link href="/shop" className="text-primary hover:underline text-sm font-medium">
                            View All
                        </Link>
                    </div>
                    {isLoading ? (
                        <div className="text-muted-foreground">Loading...</div>
                    ) : (
                        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            {categories.slice(0, 6).map((category) => (
                                <Link
                                    key={category.id}
                                    href={`/shop?category=${category.slug}`}
                                    className="group rounded-lg border p-4 text-center hover:border-primary hover:shadow-sm transition-all"
                                >
                                    <div className="mb-2 text-3xl">🥬</div>
                                    <h3 className="font-medium text-sm group-hover:text-primary">
                                        {category.name}
                                    </h3>
                                    <p className="text-xs text-muted-foreground mt-1">
                                        {category.products_count || 0} items
                                    </p>
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </section>

            {/* Markets */}
            <section className="py-16 bg-muted/30">
                <div className="container mx-auto px-4">
                    <div className="flex items-center justify-between mb-8">
                        <h2 className="text-2xl font-bold">Popular Markets</h2>
                        <Link href="/markets" className="text-primary hover:underline text-sm font-medium">
                            View All Markets
                        </Link>
                    </div>
                    {isLoading ? (
                        <div className="text-muted-foreground">Loading...</div>
                    ) : (
                        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                            {markets.map((market) => (
                                <Link
                                    key={market.id}
                                    href={`/markets/${market.id}`}
                                    className="group rounded-lg border bg-card p-6 hover:shadow-md transition-shadow"
                                >
                                    <div className="flex items-center gap-2 mb-3">
                                        <span className={`h-2 w-2 rounded-full ${market.is_open ? 'bg-green-500' : 'bg-gray-400'}`} />
                                        <span className="text-xs text-muted-foreground">
                                            {market.is_open ? 'Open' : 'Closed'}
                                        </span>
                                    </div>
                                    <h3 className="font-semibold group-hover:text-primary">{market.name}</h3>
                                    <p className="text-sm text-muted-foreground mt-1">{market.address}</p>
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </section>

            {/* CTA */}
            <section className="py-20">
                <div className="container mx-auto px-4 text-center">
                    <h2 className="text-3xl font-bold mb-4">Ready to get started?</h2>
                    <p className="text-muted-foreground mb-8 max-w-md mx-auto">
                        Join thousands of customers who trust us for their daily fresh produce needs.
                    </p>
                    <Link href="/register">
                        <Button size="lg">Create Free Account</Button>
                    </Link>
                </div>
            </section>
        </StorefrontLayout>
    );
}
