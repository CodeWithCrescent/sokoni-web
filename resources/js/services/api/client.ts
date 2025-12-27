import axios, { AxiosError, AxiosResponse } from 'axios';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    withXSRFToken: true,
});

apiClient.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error: AxiosError) => {
        // Only redirect to login if truly unauthenticated and not on an auth page
        if (error.response?.status === 401 && !window.location.pathname.includes('/login')) {
            // Check if user should be redirected (session expired)
            const isApiError = error.config?.url?.startsWith('/api');
            if (isApiError) {
                // Let the component handle the error instead of redirecting
                console.warn('API authentication error:', error.response?.data);
            }
        }
        return Promise.reject(error);
    },
);

export interface ApiResponse<T> {
    success: boolean;
    message: string;
    data: T;
}

export interface PaginatedResponse<T> {
    success: boolean;
    message: string;
    data: T[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
}

export default apiClient;
