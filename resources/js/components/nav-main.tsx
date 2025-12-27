import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { resolveUrl } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { useState } from 'react';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    const [openItems, setOpenItems] = useState<string[]>([]);

    const toggleItem = (title: string) => {
        setOpenItems(prev => 
            prev.includes(title) 
                ? prev.filter(item => item !== title)
                : [...prev, title]
        );
    };

    const renderNavItem = (item: NavItem, level: number = 0) => {
        const hasChildren = item.items && item.items.length > 0;
        const isOpen = openItems.includes(item.title);
        const isActive = item.href && page.url.startsWith(resolveUrl(item.href));

        if (hasChildren) {
            return (
                <Collapsible
                    key={item.title}
                    open={isOpen}
                    onOpenChange={() => toggleItem(item.title)}
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger asChild>
                            <SidebarMenuButton tooltip={{ children: item.title }}>
                                {item.icon && <item.icon />}
                                <span>{item.title}</span>
                                <ChevronDown className="ml-auto transition-transform duration-200" 
                                    style={{ transform: isOpen ? 'rotate(180deg)' : 'rotate(0deg)' }} />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                {item.items!.map((subItem) => (
                                    <SidebarMenuSubItem key={subItem.title}>
                                        <SidebarMenuSubButton
                                            asChild
                                            isActive={subItem.href ? page.url.startsWith(resolveUrl(subItem.href)) : false}
                                        >
                                            <Link href={subItem.href!} prefetch>
                                                {subItem.icon && <subItem.icon />}
                                                <span>{subItem.title}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                ))}
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>
            );
        }

        return (
            <SidebarMenuItem key={item.title}>
                <SidebarMenuButton
                    asChild
                    isActive={isActive || false}
                    tooltip={{ children: item.title }}
                >
                    <Link href={item.href!} prefetch>
                        {item.icon && <item.icon />}
                        <span>{item.title}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        );
    };

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Platform</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => renderNavItem(item))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
