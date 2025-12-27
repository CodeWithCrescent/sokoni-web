import apiClient, { ApiResponse, PaginatedResponse } from './client';

export interface Unit {
    id: number;
    name: string;
    abbreviation: string;
    description: string | null;
    is_active: boolean;
    products_count?: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface UnitFilters {
    search?: string;
    is_active?: boolean;
    with_trashed?: boolean;
    per_page?: number;
    page?: number;
}

export const unitsApi = {
    list: (filters: UnitFilters = {}) => apiClient.get<PaginatedResponse<Unit>>('/units', { params: filters }),

    get: (id: number) => apiClient.get<ApiResponse<Unit>>(`/units/${id}`),

    create: (data: Partial<Unit>) => apiClient.post<ApiResponse<Unit>>('/units', data),

    update: (id: number, data: Partial<Unit>) => apiClient.put<ApiResponse<Unit>>(`/units/${id}`, data),

    delete: (id: number) => apiClient.delete<ApiResponse<null>>(`/units/${id}`),

    restore: (id: number) => apiClient.post<ApiResponse<Unit>>(`/units/${id}/restore`),
};
