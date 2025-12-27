import apiClient, { ApiResponse, PaginatedResponse } from './client';
import { Market } from './markets';

export interface OrderItem {
    id: number;
    product_name: string;
    quantity: number;
    unit_name: string;
    unit_price: number;
    total_price: number;
    notes: string | null;
}

export interface Payment {
    id: number;
    payment_method: string;
    transaction_id: string | null;
    amount: number;
    currency: string;
    status: string;
    phone_number: string | null;
    paid_at: string | null;
    created_at: string;
}

export interface Order {
    id: number;
    order_number: string;
    status: string;
    subtotal: number;
    delivery_fee: number;
    service_fee: number;
    discount: number;
    total: number;
    delivery_address: string;
    delivery_phone: string;
    delivery_instructions: string | null;
    estimated_delivery_at: string | null;
    confirmed_at: string | null;
    collected_at: string | null;
    delivered_at: string | null;
    cancelled_at: string | null;
    cancellation_reason: string | null;
    is_paid: boolean;
    can_be_cancelled: boolean;
    market?: Market;
    items?: OrderItem[];
    latest_payment?: Payment;
    items_count?: number;
    created_at: string;
    updated_at: string;
}

export interface OrderFilters {
    status?: string;
    market_id?: number;
    per_page?: number;
    page?: number;
}

export interface CreateOrderData {
    market_id: number;
    items: {
        market_product_id: number;
        quantity: number;
        notes?: string;
    }[];
    delivery_address: string;
    delivery_phone: string;
    delivery_instructions?: string;
    notes?: string;
}

export const ordersApi = {
    list: (filters: OrderFilters = {}) => apiClient.get<PaginatedResponse<Order>>('/orders', { params: filters }),

    get: (id: number) => apiClient.get<ApiResponse<Order>>(`/orders/${id}`),

    create: (data: CreateOrderData) => apiClient.post<ApiResponse<Order>>('/orders', data),

    updateStatus: (id: number, status: string, notes?: string, cancellationReason?: string) =>
        apiClient.put<ApiResponse<Order>>(`/orders/${id}/status`, {
            status,
            notes,
            cancellation_reason: cancellationReason,
        }),

    assignCollector: (id: number, collectorId: number) =>
        apiClient.post<ApiResponse<Order>>(`/orders/${id}/assign-collector`, { collector_id: collectorId }),

    assignDriver: (id: number, driverId: number) =>
        apiClient.post<ApiResponse<Order>>(`/orders/${id}/assign-driver`, { driver_id: driverId }),

    myOrders: (filters: OrderFilters = {}) =>
        apiClient.get<PaginatedResponse<Order>>('/orders/my-orders', { params: filters }),
};

export const paymentsApi = {
    initiate: (orderId: number, paymentMethod: string, phoneNumber?: string) =>
        apiClient.post<ApiResponse<Payment>>('/payments/initiate', {
            order_id: orderId,
            payment_method: paymentMethod,
            phone_number: phoneNumber,
        }),

    get: (id: number) => apiClient.get<ApiResponse<Payment>>(`/payments/${id}`),

    confirmCash: (id: number) => apiClient.post<ApiResponse<Payment>>(`/payments/${id}/confirm`),

    forOrder: (orderId: number) => apiClient.get<ApiResponse<Payment[]>>(`/payments/order/${orderId}`),
};
