import { useEffect, useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { toast } from 'sonner';

import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PageHeader } from '@/components/ui/page-header';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { usersApi, rolesApi, User, Role } from '@/services/api';

interface Props {
    userId?: number;
}

export default function UserForm({ userId }: Props) {
    const [isLoading, setIsLoading] = useState(false);
    const [roles, setRoles] = useState<Role[]>([]);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        password: '',
        role_id: '',
        is_active: true,
    });

    const isEdit = !!userId;
    const breadcrumbs = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Users', href: '/admin/users' },
        { title: isEdit ? 'Edit User' : 'Create User', href: '#' },
    ];

    useEffect(() => {
        fetchRoles();
        if (userId) {
            fetchUser();
        }
    }, [userId]);

    const fetchRoles = async () => {
        try {
            const response = await rolesApi.list();
            setRoles(response.data.data);
        } catch (error) {
            toast.error('Failed to load roles');
        }
    };

    const fetchUser = async () => {
        if (!userId) return;
        try {
            const response = await usersApi.get(userId);
            const user = response.data.data;
            setFormData({
                name: user.name,
                email: user.email,
                phone: user.phone || '',
                password: '',
                role_id: user.role?.id.toString() || '',
                is_active: user.is_active,
            });
        } catch (error) {
            toast.error('Failed to load user');
            router.visit('/admin/users');
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const data: any = {
                name: formData.name,
                email: formData.email,
                phone: formData.phone || undefined,
                // role_id: parseInt(formData.role_id),
                role_id: formData.role_id,
                is_active: formData.is_active,
            };

            if (formData.password) {
                data.password = formData.password;
            }

            if (isEdit && userId) {
                await usersApi.update(userId, data);
                toast.success('User updated successfully');
            } else {
                data.password = formData.password;
                await usersApi.create(data);
                toast.success('User created successfully');
            }

            router.visit('/admin/users');
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to save user';
            toast.error(message);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={isEdit ? 'Edit User' : 'Create User'} />
            <div className="flex flex-col gap-6 p-6">
                <PageHeader
                    title={isEdit ? 'Edit User' : 'Create User'}
                    description={isEdit ? 'Update user information and role' : 'Add a new user to the system'}
                />

                <form onSubmit={handleSubmit} className="max-w-xl space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="name">Full Name</Label>
                        <Input
                            id="name"
                            value={formData.name}
                            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                            placeholder="John Doe"
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            value={formData.email}
                            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                            placeholder="john@example.com"
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="phone">Phone (Optional)</Label>
                        <Input
                            id="phone"
                            value={formData.phone}
                            onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                            placeholder="+255 xxx xxx xxx"
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="password">
                            {isEdit ? 'Password (leave blank to keep current)' : 'Password'}
                        </Label>
                        <Input
                            id="password"
                            type="password"
                            value={formData.password}
                            onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                            placeholder="••••••••"
                            required={!isEdit}
                            minLength={8}
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="role">Role</Label>
                        <Select
                            value={formData.role_id}
                            onValueChange={(value) => setFormData({ ...formData, role_id: value })}
                            required
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select a role" />
                            </SelectTrigger>
                            <SelectContent>
                                {roles.map((role) => (
                                    <SelectItem key={role.id} value={role.id.toString()}>
                                        {role.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
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
                            {isLoading ? 'Saving...' : isEdit ? 'Update User' : 'Create User'}
                        </Button>
                        <Button type="button" variant="outline" onClick={() => router.visit('/admin/users')}>
                            Cancel
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
