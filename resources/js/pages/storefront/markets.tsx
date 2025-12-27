import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { MapPin, Clock } from 'lucide-react';
import StorefrontLayout from '@/layouts/storefront-layout';
import { Badge } from '@/components/ui/badge';
import { apiClient } from '@/services/api';

interface Market {
    id: number;
    name: string;
    address: string;
    description: string | null;
    is_active: boolean;
    is_open: boolean;
    opening_time: string;
    closing_time: string;
    products_count?: number;
}

export default function Markets() {
    const [markets, setMarkets] = useState<Market[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetchMarkets();
    }, []);

    const fetchMarkets = async () => {
        try {
            const response = await apiClient.get('/browse/markets');
            setMarkets(response.data.data || []);
        } catch (error) {
            console.error('Failed to load markets');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <StorefrontLayout>
            <Head title="Markets - Browse Local Markets" />

            <div className="container mx-auto px-4 py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold mb-2">Local Markets</h1>
                    <p className="text-muted-foreground">
                        Browse fresh produce from markets across Dar es Salaam
                    </p>
                </div>

                {isLoading ? (
                    <div className="text-center py-12 text-muted-foreground">
                        Loading markets...
                    </div>
                ) : markets.length === 0 ? (
                    <div className="text-center py-12 text-muted-foreground">
                        No markets available at the moment.
                    </div>
                ) : (
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {markets.map((market) => (
                            <Link
                                key={market.id}
                                href={`/markets/${market.id}`}
                                className="group rounded-lg border bg-card overflow-hidden hover:shadow-lg transition-shadow"
                            >
                                <div className="aspect-video bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                                    <span className="text-6xl">🏪</span>
                                </div>
                                <div className="p-5">
                                    <div className="flex items-center gap-2 mb-3">
                                        <Badge variant={market.is_open ? 'default' : 'secondary'}>
                                            {market.is_open ? 'Open' : 'Closed'}
                                        </Badge>
                                    </div>
                                    <h2 className="text-xl font-semibold group-hover:text-primary mb-2">
                                        {market.name}
                                    </h2>
                                    <div className="flex items-center gap-2 text-sm text-muted-foreground mb-2">
                                        <MapPin className="h-4 w-4" />
                                        {market.address}
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                        <Clock className="h-4 w-4" />
                                        {market.opening_time} - {market.closing_time}
                                    </div>
                                    {market.description && (
                                        <p className="text-sm text-muted-foreground mt-3 line-clamp-2">
                                            {market.description}
                                        </p>
                                    )}
                                </div>
                            </Link>
                        ))}
                    </div>
                )}
            </div>
        </StorefrontLayout>
    );
}
