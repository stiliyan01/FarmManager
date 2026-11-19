import AttentionList from '@/components/farm/attention-list';
import CurrentVetCard from '@/components/farm/current-vet-card';
import LastInspectionCard from '@/components/farm/last-inspection-card';
import RecentEvents from '@/components/farm/recent-events';
import PageHeader from '@/components/page-header';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import StatCard from '@/components/stat-card';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';

// Табло на фермата. Вижда се от всички роли – бутоните горе зависят от това какво може потребителят.
export default function FarmShow({
    farm,
    myFarms,
    stats,
    overdueAnimals,
    quarantinedAnimals,
    recentEvents,
    currentVet,
    lastInspection,
    canAddAnimal,
    canAddVetEvent,
    canInspect,
    canManage,
}) {
    function switchFarm(event) {
        router.visit(route('farms.show', event.target.value));
    }

    return (
        <AppLayout farm={farm}>
            <Head title={farm.name} />

            <PageHeader title={farm.name} subtitle={`Рег. № ${farm.reg_number} · ${farm.settlement} · обл. ${farm.region}`}>
                {/* Собственик или ветеринар с няколко ферми превключва между тях */}
                {myFarms.length > 1 && (
                    <SelectInput value={farm.id} onChange={switchFarm} aria-label="Избери ферма" className="sm:w-auto">
                        {myFarms.map((myFarm) => (
                            <option key={myFarm.id} value={myFarm.id}>
                                {myFarm.name}
                            </option>
                        ))}
                    </SelectInput>
                )}

                {canAddAnimal && (
                    <Button asChild>
                        <Link href={route('farms.animals.create', farm.id)}>+ Добави животно</Link>
                    </Button>
                )}

                {canAddVetEvent && (
                    <Button asChild>
                        <Link href={route('farms.vet-events.create', farm.id)}>+ Нов запис</Link>
                    </Button>
                )}

                {canInspect && (
                    <Button asChild>
                        <Link href={route('inspections.create', { farm_id: farm.id })}>+ Нова проверка</Link>
                    </Button>
                )}

                {canManage && (
                    <Button variant="outline" asChild>
                        <Link href={route('farms.edit', farm.id)}>Управление на обекта</Link>
                    </Button>
                )}
            </PageHeader>

            <section className="mb-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                <StatCard label="Животни" value={stats.animals} note={stats.by_species} />
                <StatCard label="Под карантинен срок" value={stats.quarantined} note="мляко или месо" valueColor="text-amber-800" />
                <StatCard label="Ваксинации до 14 дни" value={stats.upcoming_vaccinations} note="по програмата" />
                <StatCard label="Просрочени ваксинации" value={stats.overdue} note="животни" valueColor="text-red-800" />
            </section>

            <div className="mb-6 grid items-start gap-4 lg:grid-cols-5">
                <Panel title="Изисква внимание" className="lg:col-span-2">
                    <AttentionList overdueAnimals={overdueAnimals} quarantinedAnimals={quarantinedAnimals} />
                </Panel>

                <Panel className="min-w-0 lg:col-span-3">
                    <div className="mb-3 flex items-baseline justify-between gap-3">
                        <h2 className="text-base font-semibold">Последни ветеринарни записи</h2>
                        <Link href={route('farms.vet-events.index', farm.id)} className="text-primary text-[13px] font-medium whitespace-nowrap">
                            Всички записи →
                        </Link>
                    </div>
                    <RecentEvents events={recentEvents} />
                </Panel>
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <CurrentVetCard vet={currentVet} />
                <LastInspectionCard inspection={lastInspection} />
            </div>
        </AppLayout>
    );
}
