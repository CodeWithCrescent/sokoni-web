export { default as apiClient } from './client';
export type { ApiResponse, PaginatedResponse } from './client';

export { productCategoriesApi } from './productCategories';
export type { ProductCategory, ProductCategoryFilters } from './productCategories';

export { unitsApi } from './units';
export type { Unit, UnitFilters } from './units';

export { productsApi } from './products';
export type { Product, ProductFilters, ProductPhoto } from './products';

export { marketCategoriesApi, marketsApi } from './markets';
export type { Market, MarketCategory, MarketFilters } from './markets';

export { ordersApi, paymentsApi } from './orders';
export type { Order, OrderItem, OrderFilters, Payment, CreateOrderData } from './orders';

export { analyticsApi } from './analytics';
export type { DashboardStats, RevenueData, TopProduct, TopMarket, OrderTrend } from './analytics';
