export type ValidationErrors = Record<string, string[]>;

export interface ErrorResponse {
    message: string;
    errors?: ValidationErrors;
}
