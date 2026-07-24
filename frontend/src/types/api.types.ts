export type ValidationErrors = Record<string, string[]>;

export interface ErrorResponse {
    message: string;
    errors?: ValidationErrors;
}

export interface ValidationError {
    message: string;
    errors: Record<string, string[]>;
}

export interface MessageResponse {
    message: string;
}
