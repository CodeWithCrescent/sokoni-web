import apiClient, { ApiResponse, PaginatedResponse } from './client';

export interface MarketCategory {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image: string | null;
    sort_order: number;
    is_active: boolean;
    markets_count?: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface Market {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    address: string | null;
    latitude: number | null;
    longitude: number | null;
    min_order_amount: number;
    photo: string | null;
    cover_photo: string | null;
    phone: string | null;
    email: string | null;
    operating_hours: Record<string, unknown> | null;
    is_active: boolean;
    category?: MarketCategory;
    products_count?: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface MarketFilters {
    search?: string;
    category_id?: number;
    is_active?: boolean;
    with_trashed?: boolean;
    per_page?: number;
    page?: number;
}

export const marketCategoriesApi = {
    list: (filters: Partial<MarketFilters> = {}) =>
        apiClient.get<PaginatedResponse<MarketCategory>>('/market-categories', { params: filters }),

    get: (id: number) => apiClient.get<ApiResponse<MarketCategory>>(`/market-categories/${id}`),

    create: (data: FormData | Partial<MarketCategory>) =>
        apiClient.post<ApiResponse<MarketCategory>>('/market-categories', data, {
            headers: data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {},
        }),

    update: (id: number, data: FormData | Partial<MarketCategory>) =>
        apiClient.put<ApiResponse<MarketCategory>>(`/market-categories/${id}`, data, {
            headers: data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {},
        }),

    delete: (id: number) => apiClient.delete<ApiResponse<null>>(`/market-categories/${id}`),

    restore: (id: number) => apiClient.post<ApiResponse<MarketCategory>>(`/market-categories/${id}/restore`),
};

export const marketsApi = {
    list: (filters: MarketFilters = {}) => apiClient.get<PaginatedResponse<Market>>('/markets', { params: filters }),

    get: (id: number) => apiClient.get<ApiResponse<Market>>(`/markets/${id}`),

    create: (data: FormData | Partial<Market>) =>
        apiClient.post<ApiResponse<Market>>('/markets', data, {
            headers: data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {},
        }),

    update: (id: number, data: FormData | Partial<Market>) =>
        apiClient.put<ApiResponse<Market>>(`/markets/${id}`, data, {
            headers: data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {},
        }),

    delete: (id: number) => apiClient.delete<ApiResponse<null>>(`/markets/${id}`),

    restore: (id: number) => apiClient.post<ApiResponse<Market>>(`/markets/${id}/restore`),
};
