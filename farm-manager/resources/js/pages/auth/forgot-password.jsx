import FormField from '@/components/form-field';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('password.email'));
    }

    return (
        <AuthLayout title="Забравена парола" description="Въведете имейла си и ще ви изпратим линк за нова парола">
            <Head title="Забравена парола" />

            {status && <div className="mb-4 text-center font-medium text-green-700">{status}</div>}

            <form onSubmit={submit} className="grid gap-5">
                <FormField label="Имейл" htmlFor="email" error={errors.email}>
                    <Input id="email" type="email" autoFocus value={data.email} onChange={(event) => setData('email', event.target.value)} />
                </FormField>

                <Button type="submit" className="w-full" disabled={processing}>
                    Изпрати линк
                </Button>

                <div className="text-muted-foreground text-center">
                    <TextLink href={route('login')}>Обратно към входа</TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}
