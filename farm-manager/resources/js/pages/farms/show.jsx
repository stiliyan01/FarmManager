import AttentionList from '@/components/farm/attention-list';
import CurrentVetCard from '@/components/farm/current-vet-card';
import RecentEventsTable from '@/components/farm/recent-events-table';
import StatCard from '@/components/farm/stat-card';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';

// Табло на фермата.
export default function FarmShow({ farm, ownerFarms, stats, overdueAnimals, quarantinedAnimals, recentEvents, currentVet, canAddAnimal }) {
    function switchFarm(event) {
        router.visit(route('farms.show', event.target.value));
    }

    return (
        <AppLayout farm={farm}>
            <Head title={farm.name} />

            <header className="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <div className="text-muted-foreground text-[13px]">
                        Рег. № {farm.reg_number} · {farm.settlement} · обл. {farm.region}
                    </div>
                    <h1 className="mt-1 text-[28px] font-bold tracking-tight">{farm.name}</h1>
                </div>

                <div className="flex flex-wrap gap-2.5">
                    {/* Собственик с няколко ферми може да превключва между тях */}
                    {ownerFarms.length > 1 && (
                        <SelectInput value={farm.id} onChange={switchFarm} aria-label="Избери ферма" className="w-auto">
                            {ownerFarms.map((ownerFarm) => (
                                <option key={ownerFarm.id} value={ownerFarm.id}>
                                    {ownerFarm.name}
                                </option>
                            ))}
                        </SelectInput>
                    )}

                    {canAddAnimal && (
                        <Button asChild>
                            <Link href={route('farms.animals.create', farm.id)}>+ Добави животно</Link>
                        </Button>
                    )}
                </div>
            </header>

            <section className="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard label="Животни" value={stats.animals} note={stats.by_species} />
                <StatCard label="Под карантинен срок" value={stats.quarantined} note="мляко или месо" valueColor="text-amber-800" />
                <StatCard label="Ваксинации до 14 дни" value={stats.upcoming_vaccinations} note="по програмата" />
                <StatCard label="Просрочени ваксинации" value={stats.overdue} note="животни, изискват действие" valueColor="text-red-800" />
            </section>

            <div className="mb-6 flex flex-wrap items-start gap-4">
                <Panel title="Изисква внимание" className="min-w-0 flex-[1_1_320px]">
                    <AttentionList overdueAnimals={overdueAnimals} quarantinedAnimals={quarantinedAnimals} />
                </Panel>

                <Panel title="Последни ветеринарни записи" className="min-w-0 flex-[2_1_480px]">
                    <RecentEventsTable events={recentEvents} />
                </Panel>
            </div>

            <CurrentVetCard vet={currentVet} />
        </AppLayout>
    );
}
