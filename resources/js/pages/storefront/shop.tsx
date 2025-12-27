import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { ShoppingCart, Filter } from 'lucide-react';
import StorefrontLayout from '@/layouts/storefront-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { apiClient } from '@/services/api';
import { toast } from 'sonner';

interface Product {
    id: number;
    name: string;
    description: string;
    category: { id: number; name: string };
    unit: { id: number; name: string; abbreviation: string };
    photos: { url: string }[];
    min_price?: number;
    max_price?: number;
}

interface Category {
    id: number;
    name: string;
    slug: string;
}

export default function Shop() {
    const [products, setProducts] = useState<Product[]>([]);
    const [categories, setCategories] = useState<Category[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [categoryFilter, setCategoryFilter] = useState('');

    useEffect(() => {
        fetchCategories();
        fetchProducts();
    }, []);

    useEffect(() => {
        const timer = setTimeout(() => {
            fetchProducts();
        }, 300);
        return () => clearTimeout(timer);
    }, [search, categoryFilter]);

    const fetchCategories = async () => {
        try {
            const response = await apiClient.get('/browse/categories');
            setCategories(response.data.data || []);
        } catch (error) {
            console.error('Failed to load categories');
        }
    };

    const fetchProducts = async () => {
        setIsLoading(true);
        try {
            const response = await apiClient.get('/browse/products', {
                params: {
                    search: search || undefined,
                    category_id: categoryFilter || undefined,
                },
            });
            setProducts(response.data.data || []);
        } catch (error) {
            console.error('Failed to load products');
        } finally {
            setIsLoading(false);
        }
    };

    const addToCart = (product: Product) => {
        const cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
        const existing = cart.find((item: any) => item.product_id === product.id);
        
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                product_id: product.id,
                name: product.name,
                unit: product.unit.abbreviation,
                price: product.min_price || 0,
                quantity: 1,
            });
        }
        
        localStorage.setItem('guest_cart', JSON.stringify(cart));
        toast.success(`${product.name} added to cart`);
        window.dispatchEvent(new Event('cart-updated'));
    };

    return (
        <StorefrontLayout>
            <Head title="Shop - Browse Products" />

            <div className="container mx-auto px-4 py-8">
                <div className="flex flex-col md:flex-row gap-6">
                    {/* Filters Sidebar */}
                    <aside className="w-full md:w-64 shrink-0">
                        <div className="sticky top-20 space-y-6">
                            <div>
                                <h3 className="font-semibold mb-3 flex items-center gap-2">
                                    <Filter className="h-4 w-4" />
                                    Filters
                                </h3>
                                <Input
                                    placeholder="Search products..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="mb-4"
                                />
                                <Select value={categoryFilter} onValueChange={setCategoryFilter}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Categories" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Categories</SelectItem>
                                        {categories.map((cat) => (
                                            <SelectItem key={cat.id} value={cat.id.toString()}>
                                                {cat.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </aside>

                    {/* Products Grid */}
                    <main className="flex-1">
                        <div className="flex items-center justify-between mb-6">
                            <h1 className="text-2xl font-bold">All Products</h1>
                            <span className="text-sm text-muted-foreground">
                                {products.length} products
                            </span>
                        </div>

                        {isLoading ? (
                            <div className="text-center py-12 text-muted-foreground">
                                Loading products...
                            </div>
                        ) : products.length === 0 ? (
                            <div className="text-center py-12 text-muted-foreground">
                                No products found. Try adjusting your filters.
                            </div>
                        ) : (
                            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                {products.map((product) => (
                                    <div
                                        key={product.id}
                                        className="group rounded-lg border bg-card overflow-hidden hover:shadow-md transition-shadow"
                                    >
                                        <div className="aspect-square bg-muted relative">
                                            {product.photos?.[0] ? (
                                                <img
                                                    src={product.photos[0].url}
                                                    alt={product.name}
                                                    className="w-full h-full object-cover"
                                                />
                                            ) : (
                                                <div className="w-full h-full flex items-center justify-center text-4xl">
                                                    🥬
                                                </div>
                                            )}
                                        </div>
                                        <div className="p-4">
                                            <Badge variant="secondary" className="text-xs mb-2">
                                                {product.category?.name}
                                            </Badge>
                                            <h3 className="font-medium text-sm mb-1 line-clamp-2">
                                                {product.name}
                                            </h3>
                                            <p className="text-xs text-muted-foreground mb-2">
                                                per {product.unit?.abbreviation}
                                            </p>
                                            {product.min_price ? (
                                                <p className="font-semibold text-primary">
                                                    TZS {product.min_price.toLocaleString()}
                                                    {product.max_price && product.max_price !== product.min_price && (
                                                        <span className="text-muted-foreground font-normal">
                                                            {' '}- {product.max_price.toLocaleString()}
                                                        </span>
                                                    )}
                                                </p>
                                            ) : (
                                                <p className="text-sm text-muted-foreground">
                                                    Price varies by market
                                                </p>
                                            )}
                                            <Button
                                                size="sm"
                                                className="w-full mt-3"
                                                onClick={() => addToCart(product)}
                                            >
                                                <ShoppingCart className="mr-2 h-4 w-4" />
                                                Add to Cart
                                            </Button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </main>
                </div>
            </div>
        </StorefrontLayout>
    );
}
