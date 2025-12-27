import { cn } from '@/lib/utils';
import { LucideIcon } from 'lucide-react';

interface StatCardProps {
    title: string;
    value: string | number;
    description?: string;
    icon?: LucideIcon;
    trend?: {
        value: number;
        isPositive: boolean;
    };
    className?: string;
}

export function StatCard({ title, value, description, icon: Icon, trend, className }: StatCardProps) {
    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="flex items-center justify-between">
                <p className="text-sm font-medium text-muted-foreground">{title}</p>
                {Icon && <Icon className="h-5 w-5 text-muted-foreground" />}
            </div>
            <div className="mt-2 flex items-baseline gap-2">
                <p className="text-2xl font-semibold">{value}</p>
                {trend && (
                    <span
                        className={cn(
                            'text-xs font-medium',
                            trend.isPositive ? 'text-green-600' : 'text-red-600',
                        )}
                    >
                        {trend.isPositive ? '+' : ''}{trend.value}%
                    </span>
                )}
            </div>
            {description && <p className="mt-1 text-xs text-muted-foreground">{description}</p>}
        </div>
    );
}
