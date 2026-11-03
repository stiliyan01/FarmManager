import DeleteUser from '@/components/delete-user';
import FormField from '@/components/form-field';
import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

export default function Profile({ mustVerifyEmail, status }) {
    const { auth } = usePage().props;

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
        name: auth.user.name,
        email: auth.user.email,
    });

    function submit(event) {
        event.preventDefault();
        patch(route('profile.update'));
    }

    return (
        <AppLayout>
            <Head title="Профил" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Данни за профила" description="Името и имейлът ви" />

                    <form onSubmit={submit} className="grid gap-5">
                        <FormField label="Име и фамилия" htmlFor="name" error={errors.name}>
                            <Input
                                id="name"
                                required
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
                                autoComplete="username"
                                value={data.email}
                                onChange={(event) => setData('email', event.target.value)}
                            />
                        </FormField>

                        {mustVerifyEmail && auth.user.email_verified_at === null && (
                            <p>
                                Имейлът ви не е потвърден.{' '}
                                <Link href={route('verification.send')} method="post" as="button" className="text-primary underline">
                                    Изпрати линк за потвърждение отново.
                                </Link>
                                {status === 'verification-link-sent' && <span className="mt-2 block text-green-700">Изпратихме ви нов линк.</span>}
                            </p>
                        )}

                        <div className="flex items-center gap-4">
                            <Button type="submit" disabled={processing}>
                                Запази
                            </Button>
                            {recentlySuccessful && <p className="text-muted-foreground">Запазено.</p>}
                        </div>
                    </form>
                </div>

                <DeleteUser />
            </SettingsLayout>
        </AppLayout>
    );
}
