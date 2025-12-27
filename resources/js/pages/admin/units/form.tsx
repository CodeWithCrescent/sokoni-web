import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PageHeader } from '@/components/ui/page-header';
import { Switch } from '@/components/ui/switch';
import { unitsApi } from '@/services/api';

interface Props {
    unitId?: number;
}

export default function UnitForm({ unitId }: Props) {
    const [isLoading, setIsLoading] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        abbreviation: '',
        is_active: true,
    });

    const isEdit = !!unitId;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Units', href: '/admin/units' },
        { title: isEdit ? 'Edit Unit' : 'Create Unit', href: '#' },
    ];

    useEffect(() => {
        if (unitId) {
            fetchUnit();
        }
    }, [unitId]);

    const fetchUnit = async () => {
        if (!unitId) return;
        try {
            const response = await unitsApi.get(unitId);
            const unit = response.data.data;
            setFormData({
                name: unit.name,
                abbreviation: unit.abbreviation,
                is_active: unit.is_active,
            });
        } catch (error) {
            toast.error('Failed to load unit');
            router.visit('/admin/units');
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            if (isEdit && unitId) {
                await unitsApi.update(unitId, formData);
                toast.success('Unit updated successfully');
            } else {
                await unitsApi.create(formData);
                toast.success('Unit created successfully');
            }
            router.visit('/admin/units');
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to save unit';
            toast.error(message);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={isEdit ? 'Edit Unit' : 'Create Unit'} />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title={isEdit ? 'Edit Unit' : 'Create Unit'}
                    description={isEdit ? 'Update unit details' : 'Add a new unit of measurement'}
                />

                <form onSubmit={handleSubmit} className="max-w-xl space-y-6">
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

                    <div className="flex gap-3">
                        <Button type="submit" disabled={isLoading}>
                            {isLoading ? 'Saving...' : isEdit ? 'Update Unit' : 'Create Unit'}
                        </Button>
                        <Button type="button" variant="outline" onClick={() => router.visit('/admin/units')}>
                            Cancel
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
