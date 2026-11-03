import FormField from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('password.confirm'), {
            onFinish: () => reset('password'),
        });
    }

    return (
        <AuthLayout title="Потвърдете паролата си" description="Това е защитена част от системата. Моля, въведете паролата си, за да продължите.">
            <Head title="Потвърждение на парола" />

            <form onSubmit={submit} className="grid gap-5">
                <FormField label="Парола" htmlFor="password" error={errors.password}>
                    <Input
                        id="password"
                        type="password"
                        autoFocus
                        autoComplete="current-password"
                        value={data.password}
                        onChange={(event) => setData('password', event.target.value)}
                    />
                </FormField>

                <Button type="submit" className="w-full" disabled={processing}>
                    Потвърди
                </Button>
            </form>
        </AuthLayout>
    );
}
