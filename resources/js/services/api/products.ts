import apiClient, { ApiResponse, PaginatedResponse } from './client';
import { ProductCategory } from './productCategories';
import { Unit } from './units';

export interface ProductPhoto {
    id: number;
    photo_url: string;
    alt_text: string | null;
    sort_order: number;
    is_primary: boolean;
}

export interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
    category?: ProductCategory;
    unit?: Unit;
    photos?: ProductPhoto[];
    primary_photo?: ProductPhoto;
    markets_count?: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface ProductFilters {
    search?: string;
    category_id?: number;
    is_active?: boolean;
    with_trashed?: boolean;
    per_page?: number;
    page?: number;
}

export const productsApi = {
    list: (filters: ProductFilters = {}) => apiClient.get<PaginatedResponse<Product>>('/products', { params: filters }),

    get: (id: number) => apiClient.get<ApiResponse<Product>>(`/products/${id}`),

    create: (data: Partial<Product>) => apiClient.post<ApiResponse<Product>>('/products', data),

    update: (id: number, data: Partial<Product>) => apiClient.put<ApiResponse<Product>>(`/products/${id}`, data),

    delete: (id: number) => apiClient.delete<ApiResponse<null>>(`/products/${id}`),

    restore: (id: number) => apiClient.post<ApiResponse<Product>>(`/products/${id}/restore`),

    uploadPhoto: (id: number, data: FormData) =>
        apiClient.post(`/products/${id}/photos`, data, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),

    deletePhoto: (productId: number, photoId: number) => apiClient.delete(`/products/${productId}/photos/${photoId}`),
};
