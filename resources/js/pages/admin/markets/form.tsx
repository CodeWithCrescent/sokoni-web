import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { PageHeader } from '@/components/ui/page-header';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { marketsApi, marketCategoriesApi, MarketCategory } from '@/services/api';

interface Props {
    marketId?: number;
}

export default function MarketForm({ marketId }: Props) {
    const [isLoading, setIsLoading] = useState(false);
    const [categories, setCategories] = useState<MarketCategory[]>([]);
    const [formData, setFormData] = useState({
        name: '',
        slug: '',
        description: '',
        address: '',
        latitude: '',
        longitude: '',
        phone: '',
        email: '',
        opening_time: '06:00',
        closing_time: '18:00',
        category_id: '',
        is_active: true,
    });

    const isEdit = !!marketId;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Markets', href: '/admin/markets' },
        { title: isEdit ? 'Edit Market' : 'Create Market', href: '#' },
    ];

    useEffect(() => {
        fetchCategories();
        if (marketId) {
            fetchMarket();
        }
    }, [marketId]);

    const fetchCategories = async () => {
        try {
            const response = await marketCategoriesApi.list();
            setCategories(response.data.data);
        } catch (error) {
            toast.error('Failed to load categories');
        }
    };

    const fetchMarket = async () => {
        if (!marketId) return;
        try {
            const response = await marketsApi.get(marketId);
            const market = response.data.data;
            setFormData({
                name: market.name,
                slug: market.slug,
                description: market.description || '',
                address: market.address || '',
                latitude: market.latitude?.toString() || '',
                longitude: market.longitude?.toString() || '',
                phone: market.phone || '',
                email: market.email || '',
                opening_time: market.opening_time || '06:00',
                closing_time: market.closing_time || '18:00',
                category_id: market.category?.id?.toString() || '',
                is_active: market.is_active,
            });
        } catch (error) {
            toast.error('Failed to load market');
            router.visit('/admin/markets');
        }
    };

    const generateSlug = (name: string) => {
        return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    };

    const handleNameChange = (name: string) => {
        setFormData({
            ...formData,
            name,
            slug: isEdit ? formData.slug : generateSlug(name),
        });
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const data: any = {
                ...formData,
                // category_id: formData.category_id ? parseInt(formData.category_id) : undefined,
                latitude: formData.latitude ? parseFloat(formData.latitude) : undefined,
                longitude: formData.longitude ? parseFloat(formData.longitude) : undefined,
            };

            if (isEdit && marketId) {
                await marketsApi.update(marketId, data);
                toast.success('Market updated successfully');
            } else {
                await marketsApi.create(data);
                toast.success('Market created successfully');
            }
            router.visit('/admin/markets');
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to save market';
            toast.error(message);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={isEdit ? 'Edit Market' : 'Create Market'} />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title={isEdit ? 'Edit Market' : 'Create Market'}
                    description={isEdit ? 'Update market details' : 'Add a new market'}
                />

                <form onSubmit={handleSubmit} className="max-w-2xl space-y-6">
                    <div className="grid md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="name">Name</Label>
                            <Input
                                id="name"
                                value={formData.name}
                                onChange={(e) => handleNameChange(e.target.value)}
                                placeholder="e.g., Kariakoo Market"
                                required
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="slug">Slug</Label>
                            <Input
                                id="slug"
                                value={formData.slug}
                                onChange={(e) => setFormData({ ...formData, slug: e.target.value })}
                                placeholder="e.g., kariakoo-market"
                                required
                            />
                        </div>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="category">Category</Label>
                        <Select
                            value={formData.category_id}
                            onValueChange={(value) => setFormData({ ...formData, category_id: value })}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select category" />
                            </SelectTrigger>
                            <SelectContent>
                                {categories.map((cat) => (
                                    <SelectItem key={cat.id} value={cat.id.toString()}>
                                        {cat.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="address">Address</Label>
                        <Input
                            id="address"
                            value={formData.address}
                            onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                            placeholder="Full address"
                            required
                        />
                    </div>

                    <div className="grid md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="latitude">Latitude (Optional)</Label>
                            <Input
                                id="latitude"
                                type="number"
                                step="any"
                                value={formData.latitude}
                                onChange={(e) => setFormData({ ...formData, latitude: e.target.value })}
                                placeholder="-6.8235"
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="longitude">Longitude (Optional)</Label>
                            <Input
                                id="longitude"
                                type="number"
                                step="any"
                                value={formData.longitude}
                                onChange={(e) => setFormData({ ...formData, longitude: e.target.value })}
                                placeholder="39.2695"
                            />
                        </div>
                    </div>

                    <div className="grid md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="phone">Phone (Optional)</Label>
                            <Input
                                id="phone"
                                value={formData.phone}
                                onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                                placeholder="+255 xxx xxx xxx"
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="email">Email (Optional)</Label>
                            <Input
                                id="email"
                                type="email"
                                value={formData.email}
                                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                placeholder="market@example.com"
                            />
                        </div>
                    </div>

                    <div className="grid md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <Label htmlFor="opening_time">Opening Time</Label>
                            <Input
                                id="opening_time"
                                type="time"
                                value={formData.opening_time}
                                onChange={(e) => setFormData({ ...formData, opening_time: e.target.value })}
                                required
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="closing_time">Closing Time</Label>
                            <Input
                                id="closing_time"
                                type="time"
                                value={formData.closing_time}
                                onChange={(e) => setFormData({ ...formData, closing_time: e.target.value })}
                                required
                            />
                        </div>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="description">Description (Optional)</Label>
                        <Textarea
                            id="description"
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            placeholder="Brief description of this market"
                            rows={3}
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Switch
                            id="is_active"
                            checked={formData.is_active}
                            onCheckedChange={(checked: boolean) => setFormData({ ...formData, is_active: checked })}
                        />
                        <Label htmlFor="is_active">Active</Label>
                    </div>

                    <div className="flex gap-3">
                        <Button type="submit" disabled={isLoading}>
                            {isLoading ? 'Saving...' : isEdit ? 'Update Market' : 'Create Market'}
                        </Button>
                        <Button type="button" variant="outline" onClick={() => router.visit('/admin/markets')}>
                            Cancel
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
