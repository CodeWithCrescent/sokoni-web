import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

interface BadgeStatusProps {
    isActive: boolean;
    activeText?: string;
    inactiveText?: string;
    className?: string;
}

export function BadgeStatus({
    isActive,
    activeText = 'Active',
    inactiveText = 'Inactive',
    className,
}: BadgeStatusProps) {
    return (
        <Badge
            variant={isActive ? 'default' : 'secondary'}
            className={cn(
                isActive ? 'bg-primary/90 hover:bg-primary' : 'bg-muted text-muted-foreground',
                className,
            )}
        >
            {isActive ? activeText : inactiveText}
        </Badge>
    );
}

interface BadgeDeletedProps {
    deletedAt: string | null;
    className?: string;
}

export function BadgeDeleted({ deletedAt, className }: BadgeDeletedProps) {
    if (!deletedAt) return null;

    return (
        <Badge variant="destructive" className={cn('bg-destructive/80', className)}>
            Deleted
        </Badge>
    );
}
