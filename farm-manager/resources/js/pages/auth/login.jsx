import FormField from '@/components/form-field';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(event) {
        event.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    }

    return (
        <AuthLayout title="Вход в системата" description="Въведете имейла и паролата си">
            <Head title="Вход" />

            {status && <div className="mb-4 text-center font-medium text-green-700">{status}</div>}

            <form onSubmit={submit} className="grid gap-5">
                <FormField label="Имейл" htmlFor="email" error={errors.email}>
                    <Input
                        id="email"
                        type="email"
                        required
                        autoFocus
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
                        autoComplete="current-password"
                        value={data.password}
                        onChange={(event) => setData('password', event.target.value)}
                    />
                </FormField>

                <div className="flex items-center gap-3">
                    <Checkbox id="remember" checked={data.remember} onCheckedChange={(checked) => setData('remember', checked)} />
                    <Label htmlFor="remember">Запомни ме</Label>
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                    Вход
                </Button>

                <div className="text-muted-foreground flex flex-col items-center gap-2 text-center">
                    {canResetPassword && <TextLink href={route('password.request')}>Забравена парола?</TextLink>}
                    <span>
                        Нямате акаунт? <TextLink href={route('register')}>Регистрация</TextLink>
                    </span>
                </div>
            </form>
        </AuthLayout>
    );
}
