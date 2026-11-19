import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import UserForm from '@/components/user/user-form';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function UserEdit({ user, roles, regions }) {
    const { data, setData, put, processing, errors } = useForm({
        name: user.name,
        email: user.email,
        phone: user.phone ?? '',
        role: user.role,
        region_id: user.region_id ?? '',
        password: '',
    });

    function submit(event) {
        event.preventDefault();
        put(route('users.update', user.id));
    }

    return (
        <AppLayout>
            <Head title={user.name} />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('users.index')} className="text-primary">
                    Потребители
                </Link>{' '}
                / {user.name}
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">{user.name}</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <UserForm data={data} setData={setData} errors={errors} roles={roles} regions={regions} isNew={false} />

                    <div className="flex flex-wrap gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Запази
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={route('users.index')}>Откажи</Link>
                        </Button>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
