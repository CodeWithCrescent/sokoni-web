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

export { usersApi, rolesApi, permissionsApi } from './users';
export type { User, Role, Permission, UserFilters, CreateUserData, UpdateUserData } from './users';

export { marketProductsApi } from './marketProducts';
export type { MarketProduct, MarketProductPrice, MarketProductFilters, CreateMarketProductData, UpdateMarketProductData } from './marketProducts';
