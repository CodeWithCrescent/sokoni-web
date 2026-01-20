import { useEffect, useState, useRef } from 'react';
import { ColumnDef } from '@tanstack/react-table';
import { Head } from '@inertiajs/react';
import { MoreHorizontal, Plus, RotateCcw, Trash2, Pencil } from 'lucide-react';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { productsApi, productCategoriesApi, unitsApi, Product, ProductCategory, Unit } from '@/services/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Products', href: '/admin/products' },
];

export default function ProductsIndex() {
    const [products, setProducts] = useState<Product[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(0);
    const [pageCount, setPageCount] = useState(1);
    const [deleteId, setDeleteId] = useState<number | null>(null);
    const [restoreId, setRestoreId] = useState<number | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const [isRestoring, setIsRestoring] = useState(false);
    const [formOpen, setFormOpen] = useState(false);
    const [editingProduct, setEditingProduct] = useState<Product | null>(null);
    const [categories, setCategories] = useState<ProductCategory[]>([]);
    const [units, setUnits] = useState<Unit[]>([]);
    const [formData, setFormData] = useState({ name: '', slug: '', description: '', category_id: '', unit_id: '', is_active: true, image: '' });
    const [isSaving, setIsSaving] = useState(false);
    const hasFetched = useRef(false);

    const fetchProducts = async () => {
        setIsLoading(true);
        try {
            const response = await productsApi.list({
                search,
                page: page + 1,
                per_page: 15,
                with_trashed: false,
            });
            setProducts(response.data.data);
            setPageCount(response.data.meta.last_page);
        } catch (error) {
            toast.error('Failed to load products');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current && page === 0 && search === '') return;
        hasFetched.current = true;
        fetchProducts();
    }, [page, search]);

    const handleDelete = async () => {
        if (!deleteId) return;
        setIsDeleting(true);
        try {
            await productsApi.delete(deleteId);
            toast.success('Product deleted successfully');
            fetchProducts();
        } catch (error) {
            toast.error('Failed to delete product');
        } finally {
            setIsDeleting(false);
            setDeleteId(null);
        }
    };

    const handleRestore = async () => {
        if (!restoreId) return;
        setIsRestoring(true);
        try {
            await productsApi.restore(restoreId);
            toast.success('Product restored successfully');
            fetchProducts();
        } catch (error) {
            toast.error('Failed to restore product');
        } finally {
            setIsRestoring(false);
            setRestoreId(null);
        }
    };

    const fetchFormData = async () => {
        try {
            const [catRes, unitRes] = await Promise.all([productCategoriesApi.list(), unitsApi.list()]);
            setCategories(catRes.data.data);
            setUnits(unitRes.data.data);
        } catch (error) {
            toast.error('Failed to load form data');
        }
    };

    const openCreateForm = () => {
        setEditingProduct(null);
        setFormData({ name: '', slug: '', description: '', category_id: '', unit_id: '', is_active: true, image: '' });
        fetchFormData();
        setFormOpen(true);
    };

    const openEditForm = (product: Product) => {
        setEditingProduct(product);
        setFormData({
            name: product.name,
            slug: product.slug,
            description: product.description || '',
            category_id: product.category?.id?.toString() || '',
            unit_id: product.unit?.id?.toString() || '',
            is_active: product.is_active,
            image: product.primary_photo?.photo_url || '',
        });
        fetchFormData();
        setFormOpen(true);
    };

    const generateSlug = (name: string) => name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

    const handleFormSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        try {
            const data = { ...formData, 
                // category_id: parseInt(formData.category_id), 
                // unit_id: parseInt(formData.unit_id) 
            };
            if (editingProduct) {
                await productsApi.update(editingProduct.id, data);
                toast.success('Product updated successfully');
            } else {
                await productsApi.create(data);
                toast.success('Product created successfully');
            }
            setFormOpen(false);
            fetchProducts();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to save product');
        } finally {
            setIsSaving(false);
        }
    };

    const columns: ColumnDef<Product>[] = [
        {
            accessorKey: 'name',
            header: 'Product',
            cell: ({ row }) => (
                <div className="flex items-center gap-3">
                    {row.original.primary_photo && (
                        <img
                            src={row.original.primary_photo.photo_url}
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
            accessorKey: 'category',
            header: 'Category',
            cell: ({ row }) => row.original.category?.name ?? '-',
        },
        {
            accessorKey: 'unit',
            header: 'Unit',
            cell: ({ row }) => (
                <span className="font-mono text-sm bg-muted px-2 py-1 rounded">
                    {row.original.unit?.abbreviation ?? '-'}
                </span>
            ),
        },
        {
            accessorKey: 'markets_count',
            header: 'Markets',
            cell: ({ row }) => row.original.markets_count ?? 0,
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
            <Head title="Products" />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Products"
                    description="Manage global products for your marketplace"
                    actions={
                        <Button onClick={openCreateForm}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Product
                        </Button>
                    }
                />

                <div className="flex items-center gap-4">
                    <Input
                        placeholder="Search products..."
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
                    data={products}
                    isLoading={isLoading}
                    pageCount={pageCount}
                    pageIndex={page}
                    onPageChange={setPage}
                />

                <ConfirmDialog
                    open={!!deleteId}
                    onOpenChange={(open) => !open && setDeleteId(null)}
                    title="Delete Product"
                    description="Are you sure you want to delete this product? It can be restored within 30 days."
                    confirmText="Delete"
                    onConfirm={handleDelete}
                    variant="destructive"
                    isLoading={isDeleting}
                />

                <ConfirmDialog
                    open={!!restoreId}
                    onOpenChange={(open) => !open && setRestoreId(null)}
                    title="Restore Product"
                    description="Are you sure you want to restore this product?"
                    confirmText="Restore"
                    onConfirm={handleRestore}
                    isLoading={isRestoring}
                />

                <Dialog open={formOpen} onOpenChange={setFormOpen}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>{editingProduct ? 'Edit Product' : 'Create Product'}</DialogTitle>
                        </DialogHeader>
                        <form onSubmit={handleFormSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value, slug: editingProduct ? formData.slug : generateSlug(e.target.value) })} required />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="slug">Slug</Label>
                                <Input id="slug" value={formData.slug} onChange={(e) => setFormData({ ...formData, slug: e.target.value })} required />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label>Category</Label>
                                    <Select value={formData.category_id} onValueChange={(v) => setFormData({ ...formData, category_id: v })}>
                                        <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                        <SelectContent>{categories.map((c) => <SelectItem key={c.id} value={c.id.toString()}>{c.name}</SelectItem>)}</SelectContent>
                                    </Select>
                                </div>
                                <div className="space-y-2">
                                    <Label>Unit</Label>
                                    <Select value={formData.unit_id} onValueChange={(v) => setFormData({ ...formData, unit_id: v })}>
                                        <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                        <SelectContent>{units.map((u) => <SelectItem key={u.id} value={u.id.toString()}>{u.name}</SelectItem>)}</SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="description">Description</Label>
                                <Textarea id="description" value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows={2} />
                            </div>
                            <div className="space-y-2">
                                <Label>Product Image</Label>
                                <ImageUpload
                                    value={formData.image}
                                    onChange={(url) => setFormData({ ...formData, image: url || '' })}
                                />
                            </div>
                            <div className="flex items-center gap-3">
                                <Switch id="is_active" checked={formData.is_active} onCheckedChange={(c: boolean) => setFormData({ ...formData, is_active: c })} />
                                <Label htmlFor="is_active">Active</Label>
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setFormOpen(false)}>Cancel</Button>
                                <Button type="submit" disabled={isSaving}>{isSaving ? 'Saving...' : editingProduct ? 'Update' : 'Create'}</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
