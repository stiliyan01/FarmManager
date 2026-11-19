import PageHeader from '@/components/page-header';
import Panel from '@/components/panel';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';

// Собственик, който още няма ферма. Останалите роли отиват направо на своето табло.
export default function Dashboard() {
    const { auth } = usePage().props;

    return (
        <AppLayout>
            <Head title="Табло" />

            <PageHeader title={`Здравейте, ${auth.user.name}`} />

            <Panel>
                Все още нямате ферма в системата. Фермата се регистрира от ОДБХ – след това ще получите покана по имейл и ще можете да я приемете
                оттам.
            </Panel>
        </AppLayout>
    );
}
