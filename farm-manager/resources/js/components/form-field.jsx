import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';

// Етикет + поле + грешка от валидацията.
export default function FormField({ label, htmlFor, error, children }) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={htmlFor}>{label}</Label>
            {children}
            <InputError message={error} />
        </div>
    );
}
