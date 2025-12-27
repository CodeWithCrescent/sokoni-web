import apiClient, { ApiResponse } from './client';

export interface DashboardStats {
    orders: {
        total: number;
        today: number;
        this_month: number;
        pending: number;
        by_status: Record<string, number>;
    };
    revenue: {
        total: number;
        today: number;
        this_month: number;
        last_month: number;
        growth_percent: number;
    };
    users: {
        total: number;
        new_this_month: number;
        active_customers: number;
    };
    catalog: {
        total_products: number;
        total_markets: number;
        active_markets: number;
    };
}

export interface RevenueData {
    period: string;
    data: { date: string; total: number }[];
}

export interface TopProduct {
    id: number;
    name: string;
    total_quantity: number;
    total_revenue: number;
    order_count: number;
}

export interface TopMarket {
    id: number;
    name: string;
    order_count: number;
    total_revenue: number;
}

export interface OrderTrend {
    date: string;
    count: number;
    revenue: number;
}

export const analyticsApi = {
    dashboard: () => apiClient.get<ApiResponse<DashboardStats>>('/analytics/dashboard'),

    revenue: (period: 'week' | 'month' | 'year' = 'month') =>
        apiClient.get<ApiResponse<RevenueData>>('/analytics/revenue', { params: { period } }),

    topProducts: (limit = 10) =>
        apiClient.get<ApiResponse<TopProduct[]>>('/analytics/top-products', { params: { limit } }),

    topMarkets: (limit = 10) =>
        apiClient.get<ApiResponse<TopMarket[]>>('/analytics/top-markets', { params: { limit } }),

    ordersTrend: (days = 30) =>
        apiClient.get<ApiResponse<OrderTrend[]>>('/analytics/orders-trend', { params: { days } }),
};
