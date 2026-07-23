import { Controller, type Control, type FieldValues, type Path } from 'react-hook-form';
import { Form, Input, type InputProps } from 'antd';

type PasswordFieldProps<T extends FieldValues> = {
    control: Control<T>;
    name: Path<T>;
    label?: string;
    required?: boolean;
} & InputProps;

export function PasswordField<T extends FieldValues>({ control, name, label, required = false, className, ...props }: PasswordFieldProps<T>) {
    return (
        <Controller
            control={control}
            name={name}
            render={({ field, fieldState }) => {
                const hasError = Boolean(fieldState.error);

                return (
                    <Form.Item
                        className="app-form-item"
                        label={label}
                        validateStatus={hasError ? 'error' : ''}
                        help={fieldState.error?.message}
                        required={required}
                        style={{ marginBottom: hasError ? 32 : 24 }}
                    >
                        <Input.Password {...field} {...props} value={field.value ?? ''} className={['h-8', className].filter(Boolean).join(' ')} />
                    </Form.Item>
                );
            }}
        />
    );
}
