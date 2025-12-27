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
import { unitsApi, Unit } from '@/services/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Units', href: '/admin/units' },
];

export default function UnitsIndex() {
    const [units, setUnits] = useState<Unit[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(0);
    const [pageCount, setPageCount] = useState(1);
    const [deleteId, setDeleteId] = useState<number | null>(null);
    const [restoreId, setRestoreId] = useState<number | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const [isRestoring, setIsRestoring] = useState(false);
    const [formOpen, setFormOpen] = useState(false);
    const [editingUnit, setEditingUnit] = useState<Unit | null>(null);
    const [formData, setFormData] = useState({ name: '', abbreviation: '', is_active: true });
    const [isSaving, setIsSaving] = useState(false);
    const hasFetched = useRef(false);

    const fetchUnits = async () => {
        setIsLoading(true);
        try {
            const response = await unitsApi.list({
                search,
                page: page + 1,
                per_page: 15,
                with_trashed: true,
            });
            setUnits(response.data.data);
            setPageCount(response.data.meta.last_page);
        } catch (error) {
            toast.error('Failed to load units');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current && page === 0 && search === '') return;
        hasFetched.current = true;
        fetchUnits();
    }, [page, search]);

    const handleDelete = async () => {
        if (!deleteId) return;
        setIsDeleting(true);
        try {
            await unitsApi.delete(deleteId);
            toast.success('Unit deleted successfully');
            fetchUnits();
        } catch (error) {
            toast.error('Failed to delete unit');
        } finally {
            setIsDeleting(false);
            setDeleteId(null);
        }
    };

    const handleRestore = async () => {
        if (!restoreId) return;
        setIsRestoring(true);
        try {
            await unitsApi.restore(restoreId);
            toast.success('Unit restored successfully');
            fetchUnits();
        } catch (error) {
            toast.error('Failed to restore unit');
        } finally {
            setIsRestoring(false);
            setRestoreId(null);
        }
    };

    const openCreateForm = () => {
        setEditingUnit(null);
        setFormData({ name: '', abbreviation: '', is_active: true });
        setFormOpen(true);
    };

    const openEditForm = (unit: Unit) => {
        setEditingUnit(unit);
        setFormData({ name: unit.name, abbreviation: unit.abbreviation, is_active: unit.is_active });
        setFormOpen(true);
    };

    const handleFormSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        try {
            if (editingUnit) {
                await unitsApi.update(editingUnit.id, formData);
                toast.success('Unit updated successfully');
            } else {
                await unitsApi.create(formData);
                toast.success('Unit created successfully');
            }
            setFormOpen(false);
            fetchUnits();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to save unit');
        } finally {
            setIsSaving(false);
        }
    };

    const columns: ColumnDef<Unit>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) => (
                <div>
                    <div className="font-medium">{row.original.name}</div>
                    <div className="text-sm text-muted-foreground">{row.original.description}</div>
                </div>
            ),
        },
        {
            accessorKey: 'abbreviation',
            header: 'Abbreviation',
            cell: ({ row }) => (
                <span className="font-mono text-sm bg-muted px-2 py-1 rounded">
                    {row.original.abbreviation}
                </span>
            ),
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
            <Head title="Units" />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Units"
                    description="Manage measurement units for products"
                    actions={
                        <Button onClick={openCreateForm}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Unit
                        </Button>
                    }
                />

                <div className="flex items-center gap-4">
                    <Input
                        placeholder="Search units..."
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
                    data={units}
                    isLoading={isLoading}
                    pageCount={pageCount}
                    pageIndex={page}
                    onPageChange={setPage}
                />

                <ConfirmDialog
                    open={!!deleteId}
                    onOpenChange={(open) => !open && setDeleteId(null)}
                    title="Delete Unit"
                    description="Are you sure you want to delete this unit? It can be restored within 30 days."
                    confirmText="Delete"
                    onConfirm={handleDelete}
                    variant="destructive"
                    isLoading={isDeleting}
                />

                <ConfirmDialog
                    open={!!restoreId}
                    onOpenChange={(open) => !open && setRestoreId(null)}
                    title="Restore Unit"
                    description="Are you sure you want to restore this unit?"
                    confirmText="Restore"
                    onConfirm={handleRestore}
                    isLoading={isRestoring}
                />

                <Dialog open={formOpen} onOpenChange={setFormOpen}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>{editingUnit ? 'Edit Unit' : 'Create Unit'}</DialogTitle>
                        </DialogHeader>
                        <form onSubmit={handleFormSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    value={formData.name}
                                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                    placeholder="e.g., Kilogram"
                                    required
                                />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="abbreviation">Abbreviation</Label>
                                <Input
                                    id="abbreviation"
                                    value={formData.abbreviation}
                                    onChange={(e) => setFormData({ ...formData, abbreviation: e.target.value })}
                                    placeholder="e.g., kg"
                                    required
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
                                <Button type="button" variant="outline" onClick={() => setFormOpen(false)}>Cancel</Button>
                                <Button type="submit" disabled={isSaving}>
                                    {isSaving ? 'Saving...' : editingUnit ? 'Update' : 'Create'}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
