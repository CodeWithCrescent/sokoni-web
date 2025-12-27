import apiClient, { ApiResponse, PaginatedResponse } from './client';
import { Market } from './markets';
import { Product } from './products';

export interface MarketProduct {
    id: number;
    market_id: number;
    product_id: number;
    price: number;
    stock: number;
    moq: number;
    is_available: boolean;
    created_at: string;
    updated_at: string;
    deleted_at?: string;
    market: Market;
    product: Product & { unit: { id: number; name: string; symbol: string } };
    bulk_prices: MarketProductPrice[];
}

export interface MarketProductPrice {
    id: number;
    market_product_id: number;
    min_qty: number;
    max_qty?: number;
    price: number;
}

export interface MarketProductFilters {
    market_id?: number;
    product_id?: number;
    is_available?: boolean;
    with_trashed?: boolean;
    search?: string;
    page?: number;
    per_page?: number;
}

export interface CreateMarketProductData {
    market_id: number;
    product_id: number;
    price: number;
    stock?: number;
    moq?: number;
    is_available?: boolean;
    bulk_prices?: Omit<MarketProductPrice, 'id' | 'market_product_id'>[];
}

export interface UpdateMarketProductData {
    price?: number;
    stock?: number;
    moq?: number;
    is_available?: boolean;
    bulk_prices?: Omit<MarketProductPrice, 'id' | 'market_product_id'>[];
}

export const marketProductsApi = {
    list: (filters?: MarketProductFilters): Promise<ApiResponse<PaginatedResponse<MarketProduct>>> => {
        return apiClient.get('/market-products', { params: filters });
    },

    get: (id: number): Promise<ApiResponse<MarketProduct>> => {
        return apiClient.get(`/market-products/${id}`);
    },

    create: (data: CreateMarketProductData): Promise<ApiResponse<MarketProduct>> => {
        return apiClient.post('/market-products', data);
    },

    update: (id: number, data: UpdateMarketProductData): Promise<ApiResponse<MarketProduct>> => {
        return apiClient.put(`/market-products/${id}`, data);
    },

    delete: (id: number): Promise<ApiResponse<null>> => {
        return apiClient.delete(`/market-products/${id}`);
    },

    restore: (id: number): Promise<ApiResponse<MarketProduct>> => {
        return apiClient.post(`/market-products/${id}/restore`);
    },
};
