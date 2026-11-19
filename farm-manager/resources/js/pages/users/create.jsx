import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import UserForm from '@/components/user/user-form';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function UserCreate({ roles, regions }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        phone: '',
        role: 'owner',
        region_id: '',
        password: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('users.store'));
    }

    return (
        <AppLayout>
            <Head title="Нов потребител" />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('users.index')} className="text-primary">
                    Потребители
                </Link>{' '}
                / Нов потребител
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">Нов потребител</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <UserForm data={data} setData={setData} errors={errors} roles={roles} regions={regions} isNew />

                    <div className="flex flex-wrap gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Създай
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
