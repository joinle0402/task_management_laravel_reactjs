import { Controller, type Control, type FieldValues, type Path } from 'react-hook-form';
import { Form, Input, type InputProps } from 'antd';

type PasswordFieldProps<T extends FieldValues> = {
    control: Control<T>;
    name: Path<T>;
    label?: string;
    required?: boolean;
} & InputProps;

export function PasswordField<T extends FieldValues>({ control, name, label, required = false, ...props }: PasswordFieldProps<T>) {
    return (
        <Controller
            control={control}
            name={name}
            render={({ field, fieldState }) => (
                <Form.Item label={label} validateStatus={fieldState.error ? 'error' : ''} help={fieldState.error?.message} required={required}>
                    <Input.Password {...field} {...props} value={field.value ?? ''} />
                </Form.Item>
            )}
        />
    );
}
