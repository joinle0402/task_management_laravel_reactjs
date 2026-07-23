import axios from 'axios';
import type { FieldValues, Path, UseFormSetError } from 'react-hook-form';
import type { MessageInstance } from 'antd/es/message/interface';

interface ApiErrorResponse {
    message?: string;
    errors?: Record<string, string[]>;
}

interface HandleApiErrorOptions<T extends FieldValues> {
    error: unknown;
    message: MessageInstance;
    setError: UseFormSetError<T>;
    fallbackMessage?: string;
}

export function handleApiError<T extends FieldValues>({
    error,
    message,
    setError,
    fallbackMessage = 'Đã xảy ra lỗi. Vui lòng thử lại.',
}: HandleApiErrorOptions<T>): void {
    console.group('API Error');
    console.error(error);
    if (axios.isAxiosError(error)) {
        console.log('Status:', error.response?.status);
        console.log('Message:', error.message);
        console.log('Config:', error.config);
        console.log('Request:', error.request);
        console.log('Response:', error.response);
        console.log('Data:', error.response?.data);
    }
    console.groupEnd();

    if (!axios.isAxiosError<ApiErrorResponse>(error)) {
        message.error(error instanceof Error ? error.message : fallbackMessage).then();
        return;
    }

    const responseData = error.response?.data;

    if (error.response?.status === 422 && responseData?.errors) {
        Object.entries(responseData.errors).forEach(([field, messages]) => {
            const message = messages[0];

            if (!message) {
                return;
            }

            setError(field as Path<T>, {
                type: 'server',
                message,
            });
        });

        return;
    }

    message.error(responseData?.message ?? error.message ?? fallbackMessage).then();
}
