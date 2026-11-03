import FormField from '@/components/form-field';
import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { Head, useForm } from '@inertiajs/react';

export default function Password() {
    const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    function submit(event) {
        event.preventDefault();
        put(route('password.update'), {
            preserveScroll: true,
            onFinish: () => reset(),
        });
    }

    return (
        <AppLayout>
            <Head title="Парола" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Смяна на паролата" description="Използвайте дълга парола, която не ползвате другаде" />

                    <form onSubmit={submit} className="grid gap-5">
                        <FormField label="Сегашна парола" htmlFor="current_password" error={errors.current_password}>
                            <Input
                                id="current_password"
                                type="password"
                                autoComplete="current-password"
                                value={data.current_password}
                                onChange={(event) => setData('current_password', event.target.value)}
                            />
                        </FormField>

                        <FormField label="Нова парола" htmlFor="password" error={errors.password}>
                            <Input
                                id="password"
                                type="password"
                                autoComplete="new-password"
                                value={data.password}
                                onChange={(event) => setData('password', event.target.value)}
                            />
                        </FormField>

                        <FormField label="Повторете новата парола" htmlFor="password_confirmation" error={errors.password_confirmation}>
                            <Input
                                id="password_confirmation"
                                type="password"
                                autoComplete="new-password"
                                value={data.password_confirmation}
                                onChange={(event) => setData('password_confirmation', event.target.value)}
                            />
                        </FormField>

                        <div className="flex items-center gap-4">
                            <Button type="submit" disabled={processing}>
                                Запази паролата
                            </Button>
                            {recentlySuccessful && <p className="text-muted-foreground">Запазено.</p>}
                        </div>
                    </form>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
