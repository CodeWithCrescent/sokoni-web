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
import { productsApi, productCategoriesApi, unitsApi, ProductCategory, Unit } from '@/services/api';

interface Props {
    productId?: number;
}

export default function ProductForm({ productId }: Props) {
    const [isLoading, setIsLoading] = useState(false);
    const [categories, setCategories] = useState<ProductCategory[]>([]);
    const [units, setUnits] = useState<Unit[]>([]);
    const [formData, setFormData] = useState({
        name: '',
        slug: '',
        description: '',
        category_id: '',
        unit_id: '',
        is_active: true,
    });

    const isEdit = !!productId;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Products', href: '/admin/products' },
        { title: isEdit ? 'Edit Product' : 'Create Product', href: '#' },
    ];

    useEffect(() => {
        fetchCategories();
        fetchUnits();
        if (productId) {
            fetchProduct();
        }
    }, [productId]);

    const fetchCategories = async () => {
        try {
            const response = await productCategoriesApi.list();
            setCategories(response.data.data);
        } catch (error) {
            toast.error('Failed to load categories');
        }
    };

    const fetchUnits = async () => {
        try {
            const response = await unitsApi.list();
            setUnits(response.data.data);
        } catch (error) {
            toast.error('Failed to load units');
        }
    };

    const fetchProduct = async () => {
        if (!productId) return;
        try {
            const response = await productsApi.get(productId);
            const product = response.data.data;
            setFormData({
                name: product.name,
                slug: product.slug,
                description: product.description || '',
                category_id: product.category?.id?.toString() || '',
                unit_id: product.unit?.id?.toString() || '',
                is_active: product.is_active,
            });
        } catch (error) {
            toast.error('Failed to load product');
            router.visit('/admin/products');
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
            const data = {
                ...formData,
                category_id: parseInt(formData.category_id),
                unit_id: parseInt(formData.unit_id),
            };

            if (isEdit && productId) {
                await productsApi.update(productId, data);
                toast.success('Product updated successfully');
            } else {
                await productsApi.create(data);
                toast.success('Product created successfully');
            }
            router.visit('/admin/products');
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to save product';
            toast.error(message);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={isEdit ? 'Edit Product' : 'Create Product'} />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title={isEdit ? 'Edit Product' : 'Create Product'}
                    description={isEdit ? 'Update product details' : 'Add a new product to the catalog'}
                />

                <form onSubmit={handleSubmit} className="max-w-xl space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="name">Name</Label>
                        <Input
                            id="name"
                            value={formData.name}
                            onChange={(e) => handleNameChange(e.target.value)}
                            placeholder="e.g., Tomatoes"
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="slug">Slug</Label>
                        <Input
                            id="slug"
                            value={formData.slug}
                            onChange={(e) => setFormData({ ...formData, slug: e.target.value })}
                            placeholder="e.g., tomatoes"
                            required
                        />
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
                        <Label htmlFor="unit">Unit</Label>
                        <Select
                            value={formData.unit_id}
                            onValueChange={(value) => setFormData({ ...formData, unit_id: value })}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select unit" />
                            </SelectTrigger>
                            <SelectContent>
                                {units.map((unit) => (
                                    <SelectItem key={unit.id} value={unit.id.toString()}>
                                        {unit.name} ({unit.abbreviation})
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="description">Description (Optional)</Label>
                        <Textarea
                            id="description"
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            placeholder="Brief description of this product"
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
                            {isLoading ? 'Saving...' : isEdit ? 'Update Product' : 'Create Product'}
                        </Button>
                        <Button type="button" variant="outline" onClick={() => router.visit('/admin/products')}>
                            Cancel
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
