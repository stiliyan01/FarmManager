import SignalList from '@/components/control/signal-list';
import VetPerformance from '@/components/control/vet-performance';
import PageHeader from '@/components/page-header';
import Panel from '@/components/panel';
import Pill from '@/components/pill';
import StatCard from '@/components/stat-card';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { inspectionResultColors } from '@/lib/colors';
import { Head, Link } from '@inertiajs/react';

// Контролното табло на инспектора за областта му.
export default function ControlIndex({ region, year, stats, vets, signals, recentInspections }) {
    let programPercent = '—';
    if (stats.program_percent !== null) {
        programPercent = `${stats.program_percent}%`;
    }

    return (
        <AppLayout>
            <Head title="Контрол" />

            <PageHeader title={`Контрол – област ${region}`} subtitle={`Държавна профилактична програма · ${year}`}>
                {/* Обикновени връзки, а не Inertia – отговорът е файл за изтегляне */}
                <Button variant="outline" asChild>
                    <a href={route('reports.region', 'pdf')}>Отчет за областта (PDF)</a>
                </Button>
                <Button variant="outline" asChild>
                    <a href={route('reports.region', 'excel')}>Excel</a>
                </Button>
                <Button asChild>
                    <Link href={route('inspections.create')}>+ Нова проверка</Link>
                </Button>
            </PageHeader>

            <section className="mb-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                <StatCard label="Ферми в областта" value={stats.farms} note={`${stats.vets} ветеринарни лекари`} />
                <StatCard label="Изпълнение на програмата" value={programPercent} note="задължителни ваксинации в срок" />
                <StatCard
                    label="Просрочени ваксинации"
                    value={stats.overdue}
                    note={`животни в ${stats.farms_with_overdue} ферми`}
                    valueColor="text-red-800"
                />
                <StatCard label="Под карантинен срок" value={stats.quarantined} note="мляко или месо" valueColor="text-amber-800" />
            </section>

            <Panel className="mb-6">
                <div className="mb-3 flex flex-wrap items-baseline justify-between gap-2">
                    <h2 className="text-base font-semibold">Изпълнение по ветеринар</h2>
                    <span className="text-muted-foreground text-[13px]">най-слабото изпълнение е най-горе</span>
                </div>
                <VetPerformance vets={vets} />
            </Panel>

            <div className="grid items-start gap-4 lg:grid-cols-2">
                <Panel title="Сигнали">
                    <SignalList signals={signals} />
                </Panel>

                <Panel>
                    <div className="mb-3 flex items-baseline justify-between gap-3">
                        <h2 className="text-base font-semibold">Последни проверки</h2>
                        <Link href={route('inspections.index')} className="text-primary text-[13px] font-medium">
                            Всички →
                        </Link>
                    </div>

                    {recentInspections.length === 0 && <p className="text-muted-foreground">Още няма проверки.</p>}

                    <ul className="divide-y">
                        {recentInspections.map((inspection) => (
                            <li key={inspection.id} className="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                                <span className="min-w-0">
                                    <Link href={route('farms.show', inspection.farm_id)} className="font-medium">
                                        {inspection.farm}
                                    </Link>
                                    <span className="text-muted-foreground"> · {inspection.date}</span>
                                </span>
                                <Pill color={inspectionResultColors[inspection.result]}>{inspection.result_label}</Pill>
                            </li>
                        ))}
                    </ul>
                </Panel>
            </div>
        </AppLayout>
    );
}
