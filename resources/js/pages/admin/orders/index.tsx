import { useEffect, useState, useRef } from 'react';
import { ColumnDef } from '@tanstack/react-table';
import { Head } from '@inertiajs/react';
import { Eye, MoreHorizontal } from 'lucide-react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { PageHeader } from '@/components/ui/page-header';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
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
import { ordersApi, Order } from '@/services/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Orders', href: '/admin/orders' },
];

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    collecting: 'bg-purple-100 text-purple-800',
    collected: 'bg-indigo-100 text-indigo-800',
    in_transit: 'bg-cyan-100 text-cyan-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    refunded: 'bg-gray-100 text-gray-800',
};

export default function OrdersIndex() {
    const [orders, setOrders] = useState<Order[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [statusFilter, setStatusFilter] = useState<string>('');
    const [page, setPage] = useState(0);
    const [pageCount, setPageCount] = useState(1);
    const hasFetched = useRef(false);

    const fetchOrders = async () => {
        setIsLoading(true);
        try {
            const response = await ordersApi.list({
                status: statusFilter || undefined,
                page: page + 1,
                per_page: 15,
            });
            setOrders(response.data.data);
            setPageCount(response.data.meta.last_page);
        } catch (error) {
            toast.error('Failed to load orders');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (hasFetched.current && page === 0 && statusFilter === '') return;
        hasFetched.current = true;
        fetchOrders();
    }, [page, statusFilter]);

    const columns: ColumnDef<Order>[] = [
        {
            accessorKey: 'order_number',
            header: 'Order #',
            cell: ({ row }) => (
                <div>
                    <div className="font-mono font-medium">{row.original.order_number}</div>
                    <div className="text-xs text-muted-foreground">
                        {new Date(row.original.created_at).toLocaleDateString()}
                    </div>
                </div>
            ),
        },
        {
            accessorKey: 'market',
            header: 'Market',
            cell: ({ row }) => row.original.market?.name ?? '-',
        },
        {
            accessorKey: 'items_count',
            header: 'Items',
            cell: ({ row }) => row.original.items_count ?? row.original.items?.length ?? 0,
        },
        {
            accessorKey: 'total',
            header: 'Total',
            cell: ({ row }) => (
                <span className="font-medium">
                    TZS {Number(row.original.total).toLocaleString()}
                </span>
            ),
        },
        {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => (
                <Badge className={statusColors[row.original.status] || 'bg-gray-100'}>
                    {row.original.status.replace('_', ' ')}
                </Badge>
            ),
        },
        {
            accessorKey: 'is_paid',
            header: 'Payment',
            cell: ({ row }) => (
                <Badge variant={row.original.is_paid ? 'default' : 'secondary'}>
                    {row.original.is_paid ? 'Paid' : 'Unpaid'}
                </Badge>
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
                        <DropdownMenuItem>
                            <Eye className="mr-2 h-4 w-4" />
                            View Details
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            ),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Orders" />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title="Orders"
                    description="Manage customer orders"
                />

                <div className="flex items-center gap-4">
                    <Select
                        value={statusFilter}
                        onValueChange={(value) => {
                            setStatusFilter(value === 'all' ? '' : value);
                            setPage(0);
                        }}
                    >
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Statuses</SelectItem>
                            <SelectItem value="pending">Pending</SelectItem>
                            <SelectItem value="confirmed">Confirmed</SelectItem>
                            <SelectItem value="collecting">Collecting</SelectItem>
                            <SelectItem value="collected">Collected</SelectItem>
                            <SelectItem value="in_transit">In Transit</SelectItem>
                            <SelectItem value="delivered">Delivered</SelectItem>
                            <SelectItem value="cancelled">Cancelled</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <DataTable
                    columns={columns}
                    data={orders}
                    isLoading={isLoading}
                    pageCount={pageCount}
                    pageIndex={page}
                    onPageChange={setPage}
                />
            </div>
        </AppLayout>
    );
}
