import type { AxiosRequestConfig } from 'axios';

import { axiosInstance } from './axios-instance';

class HttpClient {
    get<TResponse>(url: string, config?: AxiosRequestConfig): Promise<TResponse> {
        return axiosInstance.get<TResponse>(url, config) as Promise<TResponse>;
    }

    post<TResponse, TPayload = unknown>(url: string, payload?: TPayload, config?: AxiosRequestConfig<TPayload>): Promise<TResponse> {
        return axiosInstance.post<TResponse>(url, payload, config) as Promise<TResponse>;
    }

    put<TResponse, TPayload = unknown>(url: string, payload?: TPayload, config?: AxiosRequestConfig<TPayload>): Promise<TResponse> {
        return axiosInstance.put<TResponse>(url, payload, config) as Promise<TResponse>;
    }

    patch<TResponse, TPayload = unknown>(url: string, payload?: TPayload, config?: AxiosRequestConfig<TPayload>): Promise<TResponse> {
        return axiosInstance.patch<TResponse>(url, payload, config) as Promise<TResponse>;
    }

    delete<TResponse>(url: string, config?: AxiosRequestConfig): Promise<TResponse> {
        return axiosInstance.delete<TResponse>(url, config) as Promise<TResponse>;
    }
}

export const http = new HttpClient();
