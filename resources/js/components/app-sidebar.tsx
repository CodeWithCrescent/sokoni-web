import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, LayoutGrid, Package, Ruler, Store, Tags, ShoppingCart, Users, Shield, Settings, ChevronDown } from 'lucide-react';
import AppLogo from './app-logo';

function usePermissions() {
    const { auth } = usePage<{ auth: { user: { role?: { slug: string; permissions?: { slug: string }[] } } } }>().props;
    const user = auth?.user;
    const role = user?.role;
    const isAdmin = role?.slug === 'admin';
    const permissions = role?.permissions?.map((p) => p.slug) || [];

    const hasPermission = (permission: string) => {
        if (isAdmin) return true;
        return permissions.includes(permission);
    };

    return { hasPermission, isAdmin, role };
}

function getNavItems(hasPermission: (p: string) => boolean, isAdmin: boolean): NavItem[] {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    // Configuration Section
    const configurationItems: NavItem[] = [];
    if (hasPermission('product-categories.view')) {
        configurationItems.push({
            title: 'Product Categories',
            href: '/admin/product-categories',
            icon: Tags,
        });
    }
    if (hasPermission('units.view')) {
        configurationItems.push({
            title: 'Units',
            href: '/admin/units',
            icon: Ruler,
        });
    }

    if (configurationItems.length > 0) {
        items.push({
            title: 'Configuration',
            icon: Settings,
            items: configurationItems,
        });
    }

    // Core Business Items
    if (hasPermission('products.view')) {
        items.push({
            title: 'Products',
            href: '/admin/products',
            icon: Package,
        });
    }

    if (hasPermission('markets.view')) {
        items.push({
            title: 'Markets',
            href: '/admin/markets',
            icon: Store,
        });
    }

    if (hasPermission('orders.view')) {
        items.push({
            title: 'Orders',
            href: '/admin/orders',
            icon: ShoppingCart,
        });
    }

    // User Management Section
    const userManagementItems: NavItem[] = [];
    if (hasPermission('users.view')) {
        userManagementItems.push({
            title: 'Users',
            href: '/admin/users',
            icon: Users,
        });
    }
    if (hasPermission('roles.view')) {
        userManagementItems.push({
            title: 'Roles',
            href: '/admin/roles',
            icon: Shield,
        });
    }

    if (userManagementItems.length > 0) {
        items.push({
            title: 'User Management',
            icon: Users,
            items: userManagementItems,
        });
    }

    return items;
}

const footerNavItems: NavItem[] = [
    {
        title: 'API Docs',
        href: '/api/documentation',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    const { hasPermission, isAdmin } = usePermissions();
    const navItems = getNavItems(hasPermission, isAdmin);

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={navItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
