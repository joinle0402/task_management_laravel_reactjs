import * as axios from 'axios';
import type { InternalAxiosRequestConfig } from 'axios';

export const axiosInstance = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

axiosInstance.interceptors.request.use((config: InternalAxiosRequestConfig<any>): InternalAxiosRequestConfig<any> => {
    const token = localStorage.getItem('access_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

axiosInstance.interceptors.response.use(
    (response) => response.data,
    (error: unknown) => Promise.reject(error),
);
