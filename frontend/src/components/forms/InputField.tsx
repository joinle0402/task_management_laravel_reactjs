import { Controller, type Control, type FieldValues, type Path } from 'react-hook-form';
import { Form, Input, type InputProps } from 'antd';

type InputFieldProps<T extends FieldValues> = {
    control: Control<T>;
    name: Path<T>;
    label?: string;
    required?: boolean;
} & InputProps;

export function InputField<T extends FieldValues>({ control, name, label, required = false, ...props }: InputFieldProps<T>) {
    return (
        <Controller
            control={control}
            name={name}
            render={({ field, fieldState }) => (
                <Form.Item label={label} validateStatus={fieldState.error ? 'error' : ''} help={fieldState.error?.message} required={required}>
                    <Input {...field} {...props} value={field.value ?? ''} />
                </Form.Item>
            )}
        />
    );
}
