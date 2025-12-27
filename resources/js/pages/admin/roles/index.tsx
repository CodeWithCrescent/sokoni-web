import { useEffect, useState, useRef } from 'react';
import { ColumnDef } from '@tanstack/react-table';
import { Head, router } from '@inertiajs/react';
import { Plus, MoreHorizontal, Pencil, Trash2, RotateCcw, Users, Shield } from 'lucide-react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/page-header';
import { Badge } from '@/components/ui/badge';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { rolesApi, Role } from '@/services/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Roles', href: '/admin/roles' },
];

export default function RolesIndex() {
    const [roles, setRoles] = useState<Role[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [deleteId, setDeleteId] = useState<number | null>(null);
    const hasFetched = useRef(false);

    const fetchRoles = async () => {
        setIsLoading(true);
        try {
            const response = await rolesApi.list(true);
            setRoles(response.data.data);
        } catch (error) {
            toast.error('Failed to load roles');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;
        fetchRoles();
    }, []);

    const handleDelete = async () => {
        if (!deleteId) return;
        try {
            await rolesApi.delete(deleteId);
            toast.success('Role deleted successfully');
            fetchRoles();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to delete role');
        } finally {
            setDeleteId(null);
        }
    };

    const columns: ColumnDef<Role>[] = [
        {
            accessorKey: 'name',
            header: 'Role',
            cell: ({ row }) => (
                <div className="flex items-center gap-2">
                    <Shield className="h-4 w-4 text-muted-foreground" />
                    <div>
                        <div className="font-medium">{row.original.name}</div>
                        <div className="text-xs text-muted-foreground">{row.original.slug}</div>
                    </div>
                </div>
            ),
        },
        {
            accessorKey: 'description',
            header: 'Description',
            cell: ({ row }) => (
                <span className="text-sm text-muted-foreground">
                    {row.original.description || '-'}
                </span>
            ),
        },
        {
            accessorKey: 'users_count',
            header: 'Users',
            cell: ({ row }) => (
                <Badge variant="secondary" className="flex items-center gap-1 w-fit">
                    <Users className="h-3 w-3" />
                    {row.original.users_count ?? 0}
                </Badge>
            ),
        },
        {
            accessorKey: 'permissions',
            header: 'Permissions',
            cell: ({ row }) => (
                <span className="text-sm">
                    {row.original.permissions?.length ?? 0} permissions
                </span>
            ),
        },
        {
            accessorKey: 'is_default',
            header: 'Default',
            cell: ({ row }) =>
                row.original.is_default ? (
                    <Badge>Default</Badge>
                ) : null,
        },
        {
            id: 'actions',
            cell: ({ row }) => {
                const isProtected = row.original.slug === 'admin';
                return (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm">
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem onClick={() => router.visit(`/admin/roles/${row.original.id}/edit`)}>
                                <Pencil className="mr-2 h-4 w-4" />
                                Edit
                            </DropdownMenuItem>
                            {!isProtected && (row.original.users_count ?? 0) === 0 && (
                                <DropdownMenuItem onClick={() => setDeleteId(row.original.id)} className="text-destructive">
                                    <Trash2 className="mr-2 h-4 w-4" />
                                    Delete
                                </DropdownMenuItem>
                            )}
                        </DropdownMenuContent>
                    </DropdownMenu>
                );
            },
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Roles" />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Roles"
                    description="Manage user roles and their permissions"
                    actions={
                        <Button onClick={() => router.visit('/admin/roles/create')}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Role
                        </Button>
                    }
                />

                <DataTable
                    columns={columns}
                    data={roles}
                    isLoading={isLoading}
                    pageCount={1}
                    pageIndex={0}
                    onPageChange={() => {}}
                />
            </div>

            <ConfirmDialog
                open={deleteId !== null}
                onOpenChange={() => setDeleteId(null)}
                title="Delete Role"
                description="Are you sure you want to delete this role? This action cannot be undone."
                onConfirm={handleDelete}
                confirmText="Delete"
                variant="destructive"
            />
        </AppLayout>
    );
}
