import FormField from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('password.store'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    }

    return (
        <AuthLayout title="Нова парола" description="Въведете новата си парола">
            <Head title="Нова парола" />

            <form onSubmit={submit} className="grid gap-5">
                <FormField label="Имейл" htmlFor="email" error={errors.email}>
                    <Input id="email" type="email" value={data.email} readOnly />
                </FormField>

                <FormField label="Нова парола" htmlFor="password" error={errors.password}>
                    <Input
                        id="password"
                        type="password"
                        autoFocus
                        autoComplete="new-password"
                        value={data.password}
                        onChange={(event) => setData('password', event.target.value)}
                    />
                </FormField>

                <FormField label="Повторете паролата" htmlFor="password_confirmation" error={errors.password_confirmation}>
                    <Input
                        id="password_confirmation"
                        type="password"
                        autoComplete="new-password"
                        value={data.password_confirmation}
                        onChange={(event) => setData('password_confirmation', event.target.value)}
                    />
                </FormField>

                <Button type="submit" className="w-full" disabled={processing}>
                    Смени паролата
                </Button>
            </form>
        </AuthLayout>
    );
}
