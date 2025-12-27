import { useEffect, useState, useRef } from 'react';
import { Head, Link } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { ArrowLeft, Plus, Pencil, Trash2, RotateCcw, MoreHorizontal } from 'lucide-react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/page-header';
import { BadgeStatus, BadgeDeleted } from '@/components/ui/badge-status';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
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
import { marketsApi, marketProductsApi, productsApi, Market, MarketProduct, Product } from '@/services/api';

interface Props {
    market: Market;
}

export default function MarketShow({ market }: Props) {
    const [marketProducts, setMarketProducts] = useState<MarketProduct[]>([]);
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
    const [editingMarketProduct, setEditingMarketProduct] = useState<MarketProduct | null>(null);
    const [formData, setFormData] = useState({ product_id: '', price: '', stock: '', moq: '', is_available: true });
    const [isSaving, setIsSaving] = useState(false);
    const hasFetched = useRef(false);

    const fetchMarketProducts = async () => {
        setIsLoading(true);
        try {
            const response = await marketProductsApi.list({
                market_id: market.id,
                search,
                page: page + 1,
                per_page: 15,
                with_trashed: true,
            });
            setMarketProducts(response.data.data);
            setPageCount(response.data.meta.last_page);
        } catch (error) {
            toast.error('Failed to load market products');
        } finally {
            setIsLoading(false);
        }
    };

    const fetchProducts = async () => {
        try {
            const response = await productsApi.list({ per_page: 1000 });
            setProducts(response.data.data);
        } catch (error) {
            toast.error('Failed to load products');
        }
    };

    const openCreateForm = () => {
        setEditingMarketProduct(null);
        setFormData({ product_id: '', price: '', stock: '', moq: '', is_available: true });
        fetchProducts();
        setFormOpen(true);
    };

    const openEditForm = (marketProduct: MarketProduct) => {
        setEditingMarketProduct(marketProduct);
        setFormData({
            product_id: marketProduct.product_id.toString(),
            price: marketProduct.price.toString(),
            stock: marketProduct.stock.toString(),
            moq: marketProduct.moq.toString(),
            is_available: marketProduct.is_available,
        });
        fetchProducts();
        setFormOpen(true);
    };

    const handleFormSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        try {
            const data: any = {
                ...formData,
                market_id: market.id,
                product_id: parseInt(formData.product_id),
                price: parseFloat(formData.price),
                stock: parseInt(formData.stock) || 0,
                moq: parseInt(formData.moq) || 1,
            };

            if (editingMarketProduct) {
                await marketProductsApi.update(editingMarketProduct.id, data);
                toast.success('Market product updated successfully');
            } else {
                await marketProductsApi.create(data);
                toast.success('Market product added successfully');
            }
            setFormOpen(false);
            fetchMarketProducts();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to save market product');
        } finally {
            setIsSaving(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current && page === 0 && search === '') return;
        hasFetched.current = true;
        fetchMarketProducts();
    }, [page, search]);

    const handleDelete = async () => {
        if (!deleteId) return;
        setIsDeleting(true);
        try {
            await marketProductsApi.delete(deleteId);
            toast.success('Market product deleted successfully');
            fetchMarketProducts();
        } catch (error) {
            toast.error('Failed to delete market product');
        } finally {
            setIsDeleting(false);
            setDeleteId(null);
        }
    };

    const handleRestore = async () => {
        if (!restoreId) return;
        setIsRestoring(true);
        try {
            await marketProductsApi.restore(restoreId);
            toast.success('Market product restored successfully');
            fetchMarketProducts();
        } catch (error) {
            toast.error('Failed to restore market product');
        } finally {
            setIsRestoring(false);
            setRestoreId(null);
        }
    };

    const columns: ColumnDef<MarketProduct>[] = [
        {
            accessorKey: 'product',
            header: 'Product',
            cell: ({ row }) => (
                <div className="flex items-center gap-3">
                    <div>
                        <div className="font-medium">{row.original.product.name}</div>
                        <div className="text-sm text-muted-foreground">{row.original.product.unit.name}</div>
                    </div>
                </div>
            ),
        },
        {
            accessorKey: 'price',
            header: 'Price',
            cell: ({ row }) => `TZS ${Number(row.original.price).toLocaleString()}`,
        },
        {
            accessorKey: 'stock',
            header: 'Stock',
            cell: ({ row }) => row.original.stock,
        },
        {
            accessorKey: 'moq',
            header: 'MOQ',
            cell: ({ row }) => row.original.moq,
        },
        {
            accessorKey: 'is_available',
            header: 'Available',
            cell: ({ row }) => (
                <BadgeStatus isActive={row.original.is_available} />
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
        <AppLayout>
            <Head title={`${market.name} - Products`} />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title={market.name}
                    description="Manage products for this market"
                    actions={
                        <Link href="/admin/markets">
                            <Button variant="outline">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Markets
                            </Button>
                        </Link>
                    }
                />

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-white p-6 rounded-lg border">
                        <h3 className="font-semibold mb-2">Market Information</h3>
                        <div className="space-y-2 text-sm">
                            <div><span className="font-medium">Address:</span> {market.address || 'N/A'}</div>
                            <div><span className="font-medium">Category:</span> {market.category?.name || 'N/A'}</div>
                            <div><span className="font-medium">Status:</span> {market.is_active ? 'Active' : 'Inactive'}</div>
                        </div>
                    </div>
                    <div className="bg-white p-6 rounded-lg border">
                        <h3 className="font-semibold mb-2">Products Summary</h3>
                        <div className="space-y-2 text-sm">
                            <div><span className="font-medium">Total Products:</span> {marketProducts.length}</div>
                            <div><span className="font-medium">Available:</span> {marketProducts.filter(p => p.is_available).length}</div>
                            <div><span className="font-medium">Out of Stock:</span> {marketProducts.filter(p => p.stock === 0).length}</div>
                        </div>
                    </div>
                    <div className="bg-white p-6 rounded-lg border">
                        <h3 className="font-semibold mb-2">Actions</h3>
                        <div className="space-y-2">
                            <Button onClick={openCreateForm} className="w-full">
                                <Plus className="mr-2 h-4 w-4" />
                                Add Product
                            </Button>
                        </div>
                    </div>
                </div>

                <div className="flex items-center gap-4">
                    <Input
                        placeholder="Search products..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="max-w-sm"
                    />
                </div>

                <DataTable
                    columns={columns}
                    data={marketProducts}
                    isLoading={isLoading}
                    pageCount={pageCount}
                    pageIndex={page}
                    onPageChange={setPage}
                />

                <ConfirmDialog
                    open={!!deleteId}
                    onOpenChange={(open) => !open && setDeleteId(null)}
                    title="Delete Market Product"
                    description="Are you sure you want to delete this market product?"
                    confirmText="Delete"
                    onConfirm={handleDelete}
                    isLoading={isDeleting}
                />

                <ConfirmDialog
                    open={!!restoreId}
                    onOpenChange={(open) => !open && setRestoreId(null)}
                    title="Restore Market Product"
                    description="Are you sure you want to restore this market product?"
                    confirmText="Restore"
                    onConfirm={handleRestore}
                    isLoading={isRestoring}
                />

                <Dialog open={formOpen} onOpenChange={setFormOpen}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>{editingMarketProduct ? 'Edit Market Product' : 'Add Product to Market'}</DialogTitle>
                        </DialogHeader>
                        <form onSubmit={handleFormSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label>Product</Label>
                                <Select value={formData.product_id} onValueChange={(v) => setFormData({ ...formData, product_id: v })}>
                                    <SelectTrigger><SelectValue placeholder="Select product" /></SelectTrigger>
                                    <SelectContent>
                                        {products.map((p) => (
                                            <SelectItem key={p.id} value={p.id.toString()}>{p.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="price">Price (TZS)</Label>
                                    <Input id="price" type="number" step="0.01" value={formData.price} onChange={(e) => setFormData({ ...formData, price: e.target.value })} required />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="stock">Stock</Label>
                                    <Input id="stock" type="number" value={formData.stock} onChange={(e) => setFormData({ ...formData, stock: e.target.value })} />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="moq">Minimum Order Quantity</Label>
                                <Input id="moq" type="number" value={formData.moq} onChange={(e) => setFormData({ ...formData, moq: e.target.value })} />
                            </div>
                            <div className="flex items-center gap-3">
                                <Switch id="is_available" checked={formData.is_available} onCheckedChange={(c: boolean) => setFormData({ ...formData, is_available: c })} />
                                <Label htmlFor="is_available">Available</Label>
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setFormOpen(false)}>Cancel</Button>
                                <Button type="submit" disabled={isSaving}>{isSaving ? 'Saving...' : editingMarketProduct ? 'Update' : 'Add'}</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
