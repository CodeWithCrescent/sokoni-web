import { useEffect, useState, useRef } from 'react';
import { ColumnDef } from '@tanstack/react-table';
import { Head } from '@inertiajs/react';
import { MoreHorizontal, Trash2, RotateCcw, Plus, Pencil } from 'lucide-react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/page-header';
import { BadgeStatus, BadgeDeleted } from '@/components/ui/badge-status';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { ImageUpload } from '@/components/ui/image-upload';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { productCategoriesApi, ProductCategory } from '@/services/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Product Categories', href: '/admin/product-categories' },
];

export default function ProductCategoriesIndex() {
    const [categories, setCategories] = useState<ProductCategory[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(0);
    const [pageCount, setPageCount] = useState(1);
    const [deleteId, setDeleteId] = useState<number | null>(null);
    const [restoreId, setRestoreId] = useState<number | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const [isRestoring, setIsRestoring] = useState(false);
    const [formOpen, setFormOpen] = useState(false);
    const [editingCategory, setEditingCategory] = useState<ProductCategory | null>(null);
    const [formData, setFormData] = useState({ name: '', slug: '', description: '', is_active: true, image: '' });
    const [isSaving, setIsSaving] = useState(false);
    const hasFetched = useRef(false);

    const fetchCategories = async () => {
        setIsLoading(true);
        try {
            const response = await productCategoriesApi.list({
                search,
                page: page + 1,
                per_page: 15,
                with_trashed: false,
            });
            setCategories(response.data.data);
            setPageCount(response.data.meta.last_page);
        } catch (error) {
            toast.error('Failed to load categories');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current && page === 0 && search === '') return;
        hasFetched.current = true;
        fetchCategories();
    }, [page, search]);

    const handleDelete = async () => {
        if (!deleteId) return;
        setIsDeleting(true);
        try {
            await productCategoriesApi.delete(deleteId);
            toast.success('Category deleted successfully');
            fetchCategories();
        } catch (error) {
            toast.error('Failed to delete category');
        } finally {
            setIsDeleting(false);
            setDeleteId(null);
        }
    };

    const handleRestore = async () => {
        if (!restoreId) return;
        setIsRestoring(true);
        try {
            await productCategoriesApi.restore(restoreId);
            toast.success('Category restored successfully');
            fetchCategories();
        } catch (error) {
            toast.error('Failed to restore category');
        } finally {
            setIsRestoring(false);
            setRestoreId(null);
        }
    };

    const openCreateForm = () => {
        setEditingCategory(null);
        setFormData({ name: '', slug: '', description: '', is_active: true, image: '' });
        setFormOpen(true);
    };

    const openEditForm = (category: ProductCategory) => {
        setEditingCategory(category);
        setFormData({
            name: category.name,
            slug: category.slug,
            description: category.description || '',
            is_active: category.is_active,
            image: category.image || '',
        });
        setFormOpen(true);
    };

    const generateSlug = (name: string) => {
        return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    };

    const handleNameChange = (name: string) => {
        setFormData({
            ...formData,
            name,
            slug: editingCategory ? formData.slug : generateSlug(name),
        });
    };

    const handleFormSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        try {
            if (editingCategory) {
                await productCategoriesApi.update(editingCategory.id, formData);
                toast.success('Category updated successfully');
            } else {
                await productCategoriesApi.create(formData);
                toast.success('Category created successfully');
            }
            setFormOpen(false);
            fetchCategories();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to save category');
        } finally {
            setIsSaving(false);
        }
    };

    const columns: ColumnDef<ProductCategory>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) => (
                <div className="flex items-center gap-3">
                    {row.original.image && (
                        <img
                            src={row.original.image}
                            alt={row.original.name}
                            className="h-10 w-10 rounded-md object-cover"
                        />
                    )}
                    <div>
                        <div className="font-medium">{row.original.name}</div>
                        <div className="text-sm text-muted-foreground">{row.original.slug}</div>
                    </div>
                </div>
            ),
        },
        {
            accessorKey: 'products_count',
            header: 'Products',
            cell: ({ row }) => row.original.products_count ?? 0,
        },
        {
            accessorKey: 'sort_order',
            header: 'Order',
        },
        {
            accessorKey: 'is_active',
            header: 'Status',
            cell: ({ row }) => (
                <div className="flex items-center gap-2">
                    <BadgeStatus isActive={row.original.is_active} />
                    <BadgeDeleted deletedAt={row.original.deleted_at} />
                </div>
            ),
        },
        {
            id: 'actions',
            cell: ({ row }) => (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm">
                            <MoreHorizontal className="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem onClick={() => openEditForm(row.original)}>
                            <Pencil className="mr-2 h-4 w-4" />
                            Edit
                        </DropdownMenuItem>
                        {row.original.deleted_at ? (
                            <DropdownMenuItem onClick={() => setRestoreId(row.original.id)}>
                                <RotateCcw className="mr-2 h-4 w-4" />
                                Restore
                            </DropdownMenuItem>
                        ) : (
                            <DropdownMenuItem
                                onClick={() => setDeleteId(row.original.id)}
                                className="text-destructive"
                            >
                                <Trash2 className="mr-2 h-4 w-4" />
                                Delete
                            </DropdownMenuItem>
                        )}
                    </DropdownMenuContent>
                </DropdownMenu>
            ),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Product Categories" />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Product Categories"
                    description="Manage product categories for your marketplace"
                    actions={
                        <Button onClick={openCreateForm}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Category
                        </Button>
                    }
                />

                <div className="flex items-center gap-4">
                    <Input
                        placeholder="Search categories..."
                        value={search}
                        onChange={(e) => {
                            setSearch(e.target.value);
                            setPage(0);
                        }}
                        className="max-w-sm"
                    />
                </div>

                <DataTable
                    columns={columns}
                    data={categories}
                    isLoading={isLoading}
                    pageCount={pageCount}
                    pageIndex={page}
                    onPageChange={setPage}
                />

                <ConfirmDialog
                    open={!!deleteId}
                    onOpenChange={(open) => !open && setDeleteId(null)}
                    title="Delete Category"
                    description="Are you sure you want to delete this category? It can be restored within 30 days."
                    confirmText="Delete"
                    onConfirm={handleDelete}
                    variant="destructive"
                    isLoading={isDeleting}
                />

                <ConfirmDialog
                    open={!!restoreId}
                    onOpenChange={(open) => !open && setRestoreId(null)}
                    title="Restore Category"
                    description="Are you sure you want to restore this category?"
                    confirmText="Restore"
                    onConfirm={handleRestore}
                    isLoading={isRestoring}
                />

                <Dialog open={formOpen} onOpenChange={setFormOpen}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>
                                {editingCategory ? 'Edit Category' : 'Create Category'}
                            </DialogTitle>
                        </DialogHeader>
                        <form onSubmit={handleFormSubmit} className="space-y-4">
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
                                <Label htmlFor="description">Description</Label>
                                <Textarea
                                    id="description"
                                    value={formData.description}
                                    onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                    placeholder="Brief description"
                                    rows={3}
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Category Image</Label>
                                <ImageUpload
                                    value={formData.image}
                                    onChange={(url) => setFormData({ ...formData, image: url || '' })}
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
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setFormOpen(false)}>
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={isSaving}>
                                    {isSaving ? 'Saving...' : editingCategory ? 'Update' : 'Create'}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
