import apiClient, { ApiResponse, PaginatedResponse } from './client';

export interface ProductCategory {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image: string | null;
    sort_order: number;
    is_active: boolean;
    products_count?: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface ProductCategoryFilters {
    search?: string;
    is_active?: boolean;
    with_trashed?: boolean;
    per_page?: number;
    page?: number;
}

export const productCategoriesApi = {
    list: (filters: ProductCategoryFilters = {}) =>
        apiClient.get<PaginatedResponse<ProductCategory>>('/product-categories', { params: filters }),

    get: (id: number) => apiClient.get<ApiResponse<ProductCategory>>(`/product-categories/${id}`),

    create: (data: FormData | Partial<ProductCategory>) =>
        apiClient.post<ApiResponse<ProductCategory>>('/product-categories', data, {
            headers: data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {},
        }),

    update: (id: number, data: FormData | Partial<ProductCategory>) =>
        apiClient.put<ApiResponse<ProductCategory>>(`/product-categories/${id}`, data, {
            headers: data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {},
        }),

    delete: (id: number) => apiClient.delete<ApiResponse<null>>(`/product-categories/${id}`),

    restore: (id: number) => apiClient.post<ApiResponse<ProductCategory>>(`/product-categories/${id}/restore`),
};
