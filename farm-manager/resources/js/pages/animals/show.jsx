import AnimalFacts from '@/components/animal/animal-facts';
import EventTimeline from '@/components/animal/event-timeline';
import OffspringList from '@/components/animal/offspring-list';
import QuarantineBanner from '@/components/animal/quarantine-banner';
import StatusForm from '@/components/animal/status-form';
import UpcomingVaccinations from '@/components/animal/upcoming-vaccinations';
import WeightList from '@/components/animal/weight-list';
import Pill from '@/components/pill';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { animalStatusColors } from '@/lib/colors';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';

const tabs = [
    { key: 'history', title: 'Ветеринарна история' },
    { key: 'vaccinations', title: 'Ваксинации' },
    { key: 'weights', title: 'Тегло' },
    { key: 'offspring', title: 'Потомство' },
];

// Профил на животно.
export default function AnimalShow({ farm, animal, quarantine, events, vaccinations, weights, offspring, canEdit }) {
    const [activeTab, setActiveTab] = useState('history');
    const [isStatusFormOpen, setIsStatusFormOpen] = useState(false);

    const vaccinationEvents = events.filter((event) => event.type === 'vaccination');

    // Напр. „Овца · Цигая · женско · 3 г. и 2 м.“ (породата може да липсва)
    const description = [animal.species, animal.breed, animal.sex_label, animal.age].filter(Boolean).join(' · ');

    return (
        <AppLayout farm={farm}>
            <Head title={animal.ear_tag} />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('farms.show', farm.id)} className="text-primary">
                    {farm.name}
                </Link>
                {' / '}
                <Link href={route('farms.animals.index', farm.id)} className="text-primary">
                    Животни
                </Link>
                {' / '}
                {animal.ear_tag}
            </div>

            <header className="mb-5 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div className="flex flex-wrap items-center gap-2.5">
                        <h1 className="text-[28px] font-bold tracking-tight">{animal.ear_tag}</h1>
                        <Pill color={animalStatusColors[animal.status]}>{animal.status_label}</Pill>
                    </div>
                    <div className="mt-1.5 text-gray-700">{description}</div>
                </div>

                {canEdit && animal.is_active && (
                    <Button variant="outline" onClick={() => setIsStatusFormOpen(true)}>
                        Промени статус
                    </Button>
                )}
            </header>

            {isStatusFormOpen && <StatusForm animal={animal} onClose={() => setIsStatusFormOpen(false)} />}

            {quarantine && <QuarantineBanner quarantine={quarantine} />}

            <div className="flex flex-wrap items-start gap-4">
                <section className="bg-card min-w-0 flex-[2_1_480px] rounded-lg border px-5 pt-2 pb-5">
                    <div role="tablist" className="flex gap-1 overflow-x-auto border-b">
                        {tabs.map((tab) => (
                            <button
                                key={tab.key}
                                type="button"
                                role="tab"
                                aria-selected={activeTab === tab.key}
                                onClick={() => setActiveTab(tab.key)}
                                className={
                                    activeTab === tab.key
                                        ? 'text-accent-foreground border-primary h-11 border-b-2 px-3.5 font-semibold whitespace-nowrap'
                                        : 'text-muted-foreground h-11 border-b-2 border-transparent px-3.5 whitespace-nowrap'
                                }
                            >
                                {tab.title}
                            </button>
                        ))}
                    </div>

                    {activeTab === 'history' && <EventTimeline events={events} />}
                    {activeTab === 'vaccinations' && <EventTimeline events={vaccinationEvents} />}
                    {activeTab === 'weights' && <WeightList animal={animal} weights={weights} canEdit={canEdit} />}
                    {activeTab === 'offspring' && <OffspringList farm={farm} animal={animal} offspring={offspring} canEdit={canEdit} />}
                </section>

                <aside className="flex min-w-0 flex-[1_1_280px] flex-col gap-4">
                    <AnimalFacts farm={farm} animal={animal} lastWeight={weights[0]} />
                    {animal.is_active && <UpcomingVaccinations vaccinations={vaccinations} />}
                </aside>
            </div>
        </AppLayout>
    );
}
