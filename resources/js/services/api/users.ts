import apiClient, { ApiResponse, PaginatedResponse } from './client';

export interface Role {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_default: boolean;
    users_count?: number;
    permissions?: Permission[];
    created_at: string;
}

export interface Permission {
    id: number;
    name: string;
    slug: string;
    group: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    profile_photo_url: string | null;
    is_active: boolean;
    role?: Role;
    created_at: string;
    updated_at: string;
}

export interface UserFilters {
    search?: string;
    role_id?: number;
    is_active?: boolean;
    per_page?: number;
    page?: number;
}

export interface CreateUserData {
    name: string;
    email: string;
    phone?: string;
    password: string;
    role_id: number;
    is_active?: boolean;
}

export interface UpdateUserData {
    name?: string;
    email?: string;
    phone?: string;
    password?: string;
    role_id?: number;
    is_active?: boolean;
}

export const usersApi = {
    list: (filters: UserFilters = {}) =>
        apiClient.get<PaginatedResponse<User>>('/users', { params: filters }),

    get: (id: number) =>
        apiClient.get<ApiResponse<User>>(`/users/${id}`),

    create: (data: CreateUserData) =>
        apiClient.post<ApiResponse<User>>('/users', data),

    update: (id: number, data: UpdateUserData) =>
        apiClient.put<ApiResponse<User>>(`/users/${id}`, data),

    delete: (id: number) =>
        apiClient.delete<ApiResponse<null>>(`/users/${id}`),

    restore: (id: number) =>
        apiClient.post<ApiResponse<User>>(`/users/${id}/restore`),
};

export const rolesApi = {
    list: (withTrashed = false) =>
        apiClient.get<ApiResponse<Role[]>>('/roles', { params: { with_trashed: withTrashed } }),

    get: (id: number) =>
        apiClient.get<ApiResponse<Role>>(`/roles/${id}`),

    create: (data: { name: string; slug: string; description?: string; is_default?: boolean; permissions?: number[] }) =>
        apiClient.post<ApiResponse<Role>>('/roles', data),

    update: (id: number, data: { name?: string; slug?: string; description?: string; is_default?: boolean; permissions?: number[] }) =>
        apiClient.put<ApiResponse<Role>>(`/roles/${id}`, data),

    delete: (id: number) =>
        apiClient.delete<ApiResponse<null>>(`/roles/${id}`),

    restore: (id: number) =>
        apiClient.post<ApiResponse<Role>>(`/roles/${id}/restore`),
};

export const permissionsApi = {
    list: () =>
        apiClient.get<ApiResponse<Permission[]>>('/permissions'),

    grouped: () =>
        apiClient.get<ApiResponse<Record<string, Permission[]>>>('/permissions/grouped'),
};
