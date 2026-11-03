import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    function submit(event) {
        event.preventDefault();
        post(route('verification.send'));
    }

    return (
        <AuthLayout title="Потвърдете имейла си" description="Изпратихме ви имейл с линк за потвърждение. Моля, отворете го.">
            <Head title="Потвърждение на имейл" />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-center font-medium text-green-700">Изпратихме ви нов линк за потвърждение.</div>
            )}

            <form onSubmit={submit} className="grid gap-4 text-center">
                <Button type="submit" variant="secondary" disabled={processing}>
                    Изпрати линка отново
                </Button>

                <TextLink href={route('logout')} method="post" as="button" className="mx-auto">
                    Изход
                </TextLink>
            </form>
        </AuthLayout>
    );
}
