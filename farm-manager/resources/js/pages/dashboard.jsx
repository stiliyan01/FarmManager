import Panel from '@/components/panel';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';

// Временно табло – за роли, чиито екрани още не са направени, и за собственик без ферма.
export default function Dashboard() {
    const { auth } = usePage().props;

    let message = 'Таблото за вашата роля ще бъде добавено скоро.';
    if (auth.user.role === 'owner') {
        message = 'Все още нямате ферма. Когато ОДБХ регистрира ваша ферма, ще получите покана по имейл.';
    }

    return (
        <AppLayout>
            <Head title="Табло" />

            <h1 className="mb-6 text-[28px] font-bold tracking-tight">Здравейте, {auth.user.name}</h1>

            <Panel>{message}</Panel>
        </AppLayout>
    );
}
