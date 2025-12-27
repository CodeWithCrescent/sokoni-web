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
import { productCategoriesApi, ProductCategory } from '@/services/api';

interface Props {
    categoryId?: number;
}

export default function ProductCategoryForm({ categoryId }: Props) {
    const [isLoading, setIsLoading] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        slug: '',
        description: '',
        is_active: true,
    });

    const isEdit = !!categoryId;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Product Categories', href: '/admin/product-categories' },
        { title: isEdit ? 'Edit Category' : 'Create Category', href: '#' },
    ];

    useEffect(() => {
        if (categoryId) {
            fetchCategory();
        }
    }, [categoryId]);

    const fetchCategory = async () => {
        if (!categoryId) return;
        try {
            const response = await productCategoriesApi.get(categoryId);
            const cat = response.data.data;
            setFormData({
                name: cat.name,
                slug: cat.slug,
                description: cat.description || '',
                is_active: cat.is_active,
            });
        } catch (error) {
            toast.error('Failed to load category');
            router.visit('/admin/product-categories');
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
            if (isEdit && categoryId) {
                await productCategoriesApi.update(categoryId, formData);
                toast.success('Category updated successfully');
            } else {
                await productCategoriesApi.create(formData);
                toast.success('Category created successfully');
            }
            router.visit('/admin/product-categories');
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to save category';
            toast.error(message);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={isEdit ? 'Edit Category' : 'Create Category'} />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title={isEdit ? 'Edit Category' : 'Create Category'}
                    description={isEdit ? 'Update product category details' : 'Add a new product category'}
                />

                <form onSubmit={handleSubmit} className="max-w-xl space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="name">Name</Label>
                        <Input
                            id="name"
                            value={formData.name}
                            onChange={(e) => handleNameChange(e.target.value)}
                            placeholder="e.g., Vegetables"
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="slug">Slug</Label>
                        <Input
                            id="slug"
                            value={formData.slug}
                            onChange={(e) => setFormData({ ...formData, slug: e.target.value })}
                            placeholder="e.g., vegetables"
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="description">Description (Optional)</Label>
                        <Textarea
                            id="description"
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            placeholder="Brief description of this category"
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
                            {isLoading ? 'Saving...' : isEdit ? 'Update Category' : 'Create Category'}
                        </Button>
                        <Button type="button" variant="outline" onClick={() => router.visit('/admin/product-categories')}>
                            Cancel
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
