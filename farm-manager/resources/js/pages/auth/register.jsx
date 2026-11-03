import FormField from '@/components/form-field';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    }

    return (
        <AuthLayout title="Регистрация" description="Попълнете данните си, за да създадете акаунт">
            <Head title="Регистрация" />

            <form onSubmit={submit} className="grid gap-5">
                <FormField label="Име и фамилия" htmlFor="name" error={errors.name}>
                    <Input
                        id="name"
                        required
                        autoFocus
                        autoComplete="name"
                        value={data.name}
                        onChange={(event) => setData('name', event.target.value)}
                    />
                </FormField>

                <FormField label="Имейл" htmlFor="email" error={errors.email}>
                    <Input
                        id="email"
                        type="email"
                        required
                        autoComplete="email"
                        value={data.email}
                        onChange={(event) => setData('email', event.target.value)}
                    />
                </FormField>

                <FormField label="Парола" htmlFor="password" error={errors.password}>
                    <Input
                        id="password"
                        type="password"
                        required
                        autoComplete="new-password"
                        value={data.password}
                        onChange={(event) => setData('password', event.target.value)}
                    />
                </FormField>

                <FormField label="Повторете паролата" htmlFor="password_confirmation" error={errors.password_confirmation}>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        autoComplete="new-password"
                        value={data.password_confirmation}
                        onChange={(event) => setData('password_confirmation', event.target.value)}
                    />
                </FormField>

                <Button type="submit" className="w-full" disabled={processing}>
                    Създай акаунт
                </Button>

                <div className="text-muted-foreground text-center">
                    Вече имате акаунт? <TextLink href={route('login')}>Вход</TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}
