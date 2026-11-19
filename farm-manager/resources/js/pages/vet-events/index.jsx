import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import SearchForm from '@/components/search-form';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import VetEventList from '@/components/vet-event/vet-event-list';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';

// Всички ветеринарни записи във фермата – най-новите са най-горе.
export default function VetEventIndex({ farm, events, types, filters, canCreate }) {
    function applyFilters(changes) {
        router.get(route('farms.vet-events.index', farm.id), { ...filters, ...changes }, { preserveState: true });
    }

    return (
        <AppLayout farm={farm}>
            <Head title="Ветеринарни записи" />

            <PageHeader title="Ветеринарни записи" subtitle={farm.name}>
                <Button variant="outline" asChild>
                    <Link href={route('farms.reports', farm.id)}>Отчет PDF / Excel</Link>
                </Button>

                {canCreate && (
                    <Button asChild>
                        <Link href={route('farms.vet-events.create', farm.id)}>+ Нов запис</Link>
                    </Button>
                )}
            </PageHeader>

            <div className="mb-4 flex flex-wrap gap-2.5">
                <SearchForm value={filters.search} placeholder="Търси по ушна марка" onSearch={(search) => applyFilters({ search })} />

                <SelectInput
                    value={filters.type}
                    onChange={(event) => applyFilters({ type: event.target.value })}
                    aria-label="Вид на записа"
                    className="sm:w-48"
                >
                    <option value="all">Всички видове</option>
                    {types.map((type) => (
                        <option key={type.value} value={type.value}>
                            {type.label}
                        </option>
                    ))}
                </SelectInput>
            </div>

            <VetEventList events={events.data} />
            <Pagination paginator={events} />
        </AppLayout>
    );
}
