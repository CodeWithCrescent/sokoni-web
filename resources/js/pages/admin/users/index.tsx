import { useEffect, useState, useRef } from 'react';
import { ColumnDef } from '@tanstack/react-table';
import { Head, router } from '@inertiajs/react';
import { Plus, MoreHorizontal, Pencil, Trash2, RotateCcw, Shield } from 'lucide-react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/page-header';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { ConfirmDialog } from '@/components/ui/confirm-dialog';
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
import { usersApi, rolesApi, User, Role } from '@/services/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/admin/users' },
];

export default function UsersIndex() {
    const [users, setUsers] = useState<User[]>([]);
    const [roles, setRoles] = useState<Role[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [roleFilter, setRoleFilter] = useState<string>('');
    const [page, setPage] = useState(0);
    const [pageCount, setPageCount] = useState(1);
    const [deleteId, setDeleteId] = useState<number | null>(null);
    const hasFetched = useRef(false);

    const fetchUsers = async () => {
        setIsLoading(true);
        try {
            const response = await usersApi.list({
                search: search || undefined,
                role_id: roleFilter ? parseInt(roleFilter) : undefined,
                page: page + 1,
                per_page: 15,
            });
            setUsers(response.data.data);
            setPageCount(response.data.meta.last_page);
        } catch (error) {
            toast.error('Failed to load users');
        } finally {
            setIsLoading(false);
        }
    };

    const fetchRoles = async () => {
        try {
            const response = await rolesApi.list();
            setRoles(response.data.data);
        } catch (error) {
            console.error('Failed to load roles');
        }
    };

    useEffect(() => {
        if (hasFetched.current) return;
        hasFetched.current = true;
        fetchRoles();
        fetchUsers();
    }, []);

    useEffect(() => {
        if (!hasFetched.current) return;
        fetchUsers();
    }, [page, search, roleFilter]);

    const handleDelete = async () => {
        if (!deleteId) return;
        try {
            await usersApi.delete(deleteId);
            toast.success('User deactivated successfully');
            fetchUsers();
        } catch (error) {
            toast.error('Failed to deactivate user');
        } finally {
            setDeleteId(null);
        }
    };

    const handleRestore = async (id: number) => {
        try {
            await usersApi.restore(id);
            toast.success('User reactivated successfully');
            fetchUsers();
        } catch (error) {
            toast.error('Failed to reactivate user');
        }
    };

    const columns: ColumnDef<User>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) => (
                <div>
                    <div className="font-medium">{row.original.name}</div>
                    <div className="text-xs text-muted-foreground">{row.original.email}</div>
                </div>
            ),
        },
        {
            accessorKey: 'phone',
            header: 'Phone',
            cell: ({ row }) => row.original.phone || '-',
        },
        {
            accessorKey: 'role',
            header: 'Role',
            cell: ({ row }) => (
                <Badge variant="outline" className="flex items-center gap-1 w-fit">
                    <Shield className="h-3 w-3" />
                    {row.original.role?.name || 'No Role'}
                </Badge>
            ),
        },
        {
            accessorKey: 'is_active',
            header: 'Status',
            cell: ({ row }) => (
                <Badge variant={row.original.is_active ? 'default' : 'secondary'}>
                    {row.original.is_active ? 'Active' : 'Inactive'}
                </Badge>
            ),
        },
        {
            accessorKey: 'created_at',
            header: 'Joined',
            cell: ({ row }) => new Date(row.original.created_at).toLocaleDateString(),
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
                        <DropdownMenuItem onClick={() => router.visit(`/admin/users/${row.original.id}/edit`)}>
                            <Pencil className="mr-2 h-4 w-4" />
                            Edit
                        </DropdownMenuItem>
                        {row.original.is_active ? (
                            <DropdownMenuItem onClick={() => setDeleteId(row.original.id)} className="text-destructive">
                                <Trash2 className="mr-2 h-4 w-4" />
                                Deactivate
                            </DropdownMenuItem>
                        ) : (
                            <DropdownMenuItem onClick={() => handleRestore(row.original.id)}>
                                <RotateCcw className="mr-2 h-4 w-4" />
                                Reactivate
                            </DropdownMenuItem>
                        )}
                    </DropdownMenuContent>
                </DropdownMenu>
            ),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Users"
                    description="Manage system users and their roles"
                    actions={
                        <Button onClick={() => router.visit('/admin/users/create')}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add User
                        </Button>
                    }
                />

                <div className="flex items-center gap-4">
                    <Input
                        placeholder="Search users..."
                        value={search}
                        onChange={(e) => {
                            setSearch(e.target.value);
                            setPage(0);
                        }}
                        className="max-w-sm"
                    />
                    <Select
                        value={roleFilter}
                        onValueChange={(value) => {
                            setRoleFilter(value === 'all' ? '' : value);
                            setPage(0);
                        }}
                    >
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Roles</SelectItem>
                            {roles.map((role) => (
                                <SelectItem key={role.id} value={role.id.toString()}>
                                    {role.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <DataTable
                    columns={columns}
                    data={users}
                    isLoading={isLoading}
                    pageCount={pageCount}
                    pageIndex={page}
                    onPageChange={setPage}
                />
            </div>

            <ConfirmDialog
                open={deleteId !== null}
                onOpenChange={() => setDeleteId(null)}
                title="Deactivate User"
                description="Are you sure you want to deactivate this user? They will no longer be able to access the system."
                onConfirm={handleDelete}
                confirmText="Deactivate"
                variant="destructive"
            />
        </AppLayout>
    );
}
