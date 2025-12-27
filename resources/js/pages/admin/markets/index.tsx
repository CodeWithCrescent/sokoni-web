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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
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
import { marketsApi, marketCategoriesApi, Market, MarketCategory } from '@/services/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Markets', href: '/admin/markets' },
];

export default function MarketsIndex() {
    const [markets, setMarkets] = useState<Market[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(0);
    const [pageCount, setPageCount] = useState(1);
    const [deleteId, setDeleteId] = useState<number | null>(null);
    const [restoreId, setRestoreId] = useState<number | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const [isRestoring, setIsRestoring] = useState(false);
    const [formOpen, setFormOpen] = useState(false);
    const [editingMarket, setEditingMarket] = useState<Market | null>(null);
    const [marketCategories, setMarketCategories] = useState<MarketCategory[]>([]);
    const [formData, setFormData] = useState({ name: '', slug: '', address: '', description: '', opening_time: '06:00', closing_time: '18:00', is_active: true, category_id: '' });
    const [isSaving, setIsSaving] = useState(false);
    const hasFetched = useRef(false);

    const fetchMarketCategories = async () => {
        try {
            const response = await marketCategoriesApi.list();
            setMarketCategories(response.data.data);
        } catch (error) {
            toast.error('Failed to load market categories');
        }
    };

    const openCreateForm = () => {
        setEditingMarket(null);
        setFormData({ name: '', slug: '', address: '', description: '', opening_time: '06:00', closing_time: '18:00', is_active: true, category_id: '' });
        fetchMarketCategories();
        setFormOpen(true);
    };

    const openEditForm = (market: Market) => {
        setEditingMarket(market);
        setFormData({
            name: market.name, slug: market.slug, address: market.address || '',
            description: market.description || '', opening_time: '06:00',
            closing_time: '18:00', is_active: market.is_active,
            category_id: market.category?.id?.toString() || '',
        });
        fetchMarketCategories();
        setFormOpen(true);
    };

    const generateSlug = (name: string) => name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

    const handleFormSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        try {
            const data: any = { ...formData };
            if (data.category_id) {
                data.category_id = parseInt(data.category_id);
            }
            
            if (editingMarket) {
                await marketsApi.update(editingMarket.id, data);
                toast.success('Market updated successfully');
            } else {
                await marketsApi.create(data);
                toast.success('Market created successfully');
            }
            setFormOpen(false);
            fetchMarkets();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to save market');
        } finally {
            setIsSaving(false);
        }
    };

    const fetchMarkets = async () => {
        setIsLoading(true);
        try {
            const response = await marketsApi.list({
                search,
                page: page + 1,
                per_page: 15,
                with_trashed: false,
            });
            setMarkets(response.data.data);
            setPageCount(response.data.meta.last_page);
        } catch (error) {
            toast.error('Failed to load markets');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current && page === 0 && search === '') return;
        hasFetched.current = true;
        fetchMarkets();
    }, [page, search]);

    const handleDelete = async () => {
        if (!deleteId) return;
        setIsDeleting(true);
        try {
            await marketsApi.delete(deleteId);
            toast.success('Market deleted successfully');
            fetchMarkets();
        } catch (error) {
            toast.error('Failed to delete market');
        } finally {
            setIsDeleting(false);
            setDeleteId(null);
        }
    };

    const handleRestore = async () => {
        if (!restoreId) return;
        setIsRestoring(true);
        try {
            await marketsApi.restore(restoreId);
            toast.success('Market restored successfully');
            fetchMarkets();
        } catch (error) {
            toast.error('Failed to restore market');
        } finally {
            setIsRestoring(false);
            setRestoreId(null);
        }
    };

    const columns: ColumnDef<Market>[] = [
        {
            accessorKey: 'name',
            header: 'Market',
            cell: ({ row }) => (
                <div className="flex items-center gap-3">
                    {row.original.photo && (
                        <img
                            src={row.original.photo}
                            alt={row.original.name}
                            className="h-10 w-10 rounded-md object-cover"
                        />
                    )}
                    <div>
                        <div className="font-medium">{row.original.name}</div>
                        <div className="text-sm text-muted-foreground">{row.original.address}</div>
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
            accessorKey: 'min_order_amount',
            header: 'Min Order',
            cell: ({ row }) => `KES ${Number(row.original.min_order_amount).toLocaleString()}`,
        },
        {
            accessorKey: 'products_count',
            header: 'Products',
            cell: ({ row }) => row.original.products_count ?? 0,
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
            <Head title="Markets" />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Markets"
                    description="Manage markets in your marketplace"
                    actions={
                        <Button onClick={openCreateForm}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Market
                        </Button>
                    }
                />

                <div className="flex items-center gap-4">
                    <Input
                        placeholder="Search markets..."
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
                    data={markets}
                    isLoading={isLoading}
                    pageCount={pageCount}
                    pageIndex={page}
                    onPageChange={setPage}
                />

                <ConfirmDialog
                    open={!!deleteId}
                    onOpenChange={(open) => !open && setDeleteId(null)}
                    title="Delete Market"
                    description="Are you sure you want to delete this market? It can be restored within 30 days."
                    confirmText="Delete"
                    onConfirm={handleDelete}
                    variant="destructive"
                    isLoading={isDeleting}
                />

                <ConfirmDialog
                    open={!!restoreId}
                    onOpenChange={(open) => !open && setRestoreId(null)}
                    title="Restore Market"
                    description="Are you sure you want to restore this market?"
                    confirmText="Restore"
                    onConfirm={handleRestore}
                    isLoading={isRestoring}
                />

                <Dialog open={formOpen} onOpenChange={setFormOpen}>
                    <DialogContent className="max-w-lg">
                        <DialogHeader>
                            <DialogTitle>{editingMarket ? 'Edit Market' : 'Create Market'}</DialogTitle>
                        </DialogHeader>
                        <form onSubmit={handleFormSubmit} className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Name</Label>
                                    <Input id="name" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value, slug: editingMarket ? formData.slug : generateSlug(e.target.value) })} required />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="slug">Slug</Label>
                                    <Input id="slug" value={formData.slug} onChange={(e) => setFormData({ ...formData, slug: e.target.value })} required />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="address">Address</Label>
                                <Input id="address" value={formData.address} onChange={(e) => setFormData({ ...formData, address: e.target.value })} required />
                            </div>
                            <div className="space-y-2">
                                <Label>Category</Label>
                                <Select value={formData.category_id} onValueChange={(v) => setFormData({ ...formData, category_id: v })}>
                                    <SelectTrigger><SelectValue placeholder="Select category (optional)" /></SelectTrigger>
                                    <SelectContent>
                                        {marketCategories.map((c) => (
                                            <SelectItem key={c.id} value={c.id.toString()}>{c.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="opening_time">Opening Time</Label>
                                    <Input id="opening_time" type="time" value={formData.opening_time} onChange={(e) => setFormData({ ...formData, opening_time: e.target.value })} />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="closing_time">Closing Time</Label>
                                    <Input id="closing_time" type="time" value={formData.closing_time} onChange={(e) => setFormData({ ...formData, closing_time: e.target.value })} />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="description">Description</Label>
                                <Textarea id="description" value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows={2} />
                            </div>
                            <div className="flex items-center gap-3">
                                <Switch id="is_active" checked={formData.is_active} onCheckedChange={(c: boolean) => setFormData({ ...formData, is_active: c })} />
                                <Label htmlFor="is_active">Active</Label>
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setFormOpen(false)}>Cancel</Button>
                                <Button type="submit" disabled={isSaving}>{isSaving ? 'Saving...' : editingMarket ? 'Update' : 'Create'}</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
