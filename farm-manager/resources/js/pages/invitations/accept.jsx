import FormField from '@/components/form-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/auth-layout';
import { Head, Link, useForm } from '@inertiajs/react';

// Приемане на покана за ферма. step: register – нов акаунт, login – трябва първо да влезе, accept – само потвърждава.
export default function InvitationAccept({ invitation, step, problem }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        password: '',
        password_confirmation: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('invitations.accept', invitation.token), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    }

    return (
        <AuthLayout title="Покана за ферма" description={`${invitation.farm} · рег. № ${invitation.reg_number} · ${invitation.settlement}`}>
            <Head title="Покана за ферма" />

            {problem && (
                <div className="grid gap-4">
                    <p className="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-red-800">{problem}</p>
                    <Button variant="outline" asChild>
                        <Link href={route('dashboard')}>Към системата</Link>
                    </Button>
                </div>
            )}

            {!problem && step === 'login' && (
                <div className="grid gap-4">
                    <p>
                        Вече имате акаунт с имейл <strong className="break-all">{invitation.email}</strong>. Влезте в него – след входа ще се върнете
                        тук, за да приемете поканата.
                    </p>
                    <Button asChild>
                        <Link href={route('login')}>Вход</Link>
                    </Button>
                </div>
            )}

            {!problem && step === 'accept' && (
                <form onSubmit={submit} className="grid gap-4">
                    <p>ОДБХ ви кани като собственик на фермата. След като приемете, ще виждате животните и ветеринарните записи в нея.</p>
                    <InputError message={errors.invitation} />
                    <Button type="submit" disabled={processing}>
                        Приеми поканата
                    </Button>
                </form>
            )}

            {!problem && step === 'register' && (
                <form onSubmit={submit} className="grid gap-5">
                    <p className="text-muted-foreground">Създайте акаунт, за да станете собственик на фермата в системата.</p>

                    <FormField label="Имейл" htmlFor="email">
                        <Input id="email" value={invitation.email} readOnly disabled />
                    </FormField>

                    <FormField label="Име и фамилия" htmlFor="name" error={errors.name}>
                        <Input id="name" required autoFocus value={data.name} onChange={(event) => setData('name', event.target.value)} />
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

                    <InputError message={errors.invitation} />

                    <Button type="submit" className="w-full" disabled={processing}>
                        Създай акаунт и приеми
                    </Button>
                </form>
            )}
        </AuthLayout>
    );
}
