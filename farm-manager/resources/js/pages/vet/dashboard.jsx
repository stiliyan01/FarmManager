import PageHeader from '@/components/page-header';
import Panel from '@/components/panel';
import Pill from '@/components/pill';
import StatCard from '@/components/stat-card';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';

// Етикетът до фермата: първо просрочените, после предстоящите, иначе „в срок“.
function farmBadge(farm) {
    if (farm.overdue > 0) {
        return <Pill color="red">просрочени: {farm.overdue}</Pill>;
    }

    if (farm.upcoming > 0) {
        return <Pill color="amber">предстоящи: {farm.upcoming}</Pill>;
    }

    return <Pill color="green">в срок</Pill>;
}

// „Моят ден“ – таблото на ветеринаря.
export default function VetDashboard({ today, farms, totals, upcoming }) {
    return (
        <AppLayout>
            <Head title="Моят ден" />

            <PageHeader title="Моят ден" subtitle={today} />

            {farms.length === 0 && <Panel>Все още нямате назначени ферми. Обслужващият ветеринар на ферма се назначава от ОДБХ.</Panel>}

            {farms.length > 0 && (
                <>
                    <section className="mb-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                        <StatCard label="Моите ферми" value={farms.length} note={`${totals.animals} активни животни`} />
                        <StatCard label="Под карантинен срок" value={totals.quarantined} note="животни" valueColor="text-amber-800" />
                        <StatCard label="Ваксинации до 14 дни" value={totals.upcoming} note="по програмата" />
                        <StatCard label="Просрочени ваксинации" value={totals.overdue} note="животни" valueColor="text-red-800" />
                    </section>

                    <div className="grid items-start gap-4 lg:grid-cols-5">
                        <Panel title="Моите ферми" className="lg:col-span-3">
                            <ul className="divide-y">
                                {farms.map((farm) => (
                                    <li key={farm.id} className="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                                        <div className="min-w-0">
                                            <Link href={route('farms.show', farm.id)} className="text-primary font-semibold">
                                                {farm.name}
                                            </Link>
                                            <div className="text-muted-foreground mt-0.5 text-[13px]">
                                                {farm.settlement} · {farm.animals} животни
                                                {farm.quarantined > 0 && ` · ${farm.quarantined} под карантина`}
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-2.5">
                                            {farmBadge(farm)}
                                            <Button size="sm" variant="outline" asChild>
                                                <Link href={route('farms.vet-events.create', farm.id)}>+ Запис</Link>
                                            </Button>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </Panel>

                        <Panel title="Предстоящи ваксинации" className="lg:col-span-2">
                            {upcoming.length === 0 && <p className="text-muted-foreground">Няма ваксинации в следващите 14 дни.</p>}

                            <ul className="flex flex-col gap-2.5">
                                {upcoming.map((row) => (
                                    <li key={row.key} className="flex gap-3">
                                        <span className="w-20 shrink-0 font-semibold">{row.due_date}</span>
                                        <span className="text-gray-700">
                                            <Link href={route('farms.show', row.farm_id)} className="font-medium text-gray-900">
                                                {row.farm}
                                            </Link>{' '}
                                            – {row.scheme} ({row.animal_count})
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        </Panel>
                    </div>
                </>
            )}
        </AppLayout>
    );
}
