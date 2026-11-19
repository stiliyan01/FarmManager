import AnimalList from '@/components/animal/animal-list';
import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import SearchForm from '@/components/search-form';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';

export default function AnimalIndex({ farm, animals, filters, canAddAnimal }) {
    // Търсенето и филтърът се правят в сървъра – той връща по 10 животни на страница.
    function applyFilters(changes) {
        router.get(route('farms.animals.index', farm.id), { ...filters, ...changes }, { preserveState: true });
    }

    return (
        <AppLayout farm={farm}>
            <Head title="Животни" />

            <PageHeader title="Животни" subtitle={farm.name}>
                {canAddAnimal && (
                    <Button asChild>
                        <Link href={route('farms.animals.create', farm.id)}>+ Добави животно</Link>
                    </Button>
                )}
            </PageHeader>

            <div className="mb-4 flex flex-wrap gap-2.5">
                <SearchForm value={filters.search} placeholder="Търси по ушна марка" onSearch={(search) => applyFilters({ search })} />

                <SelectInput
                    value={filters.status}
                    onChange={(event) => applyFilters({ status: event.target.value })}
                    aria-label="Статус"
                    className="sm:w-44"
                >
                    <option value="active">Активни</option>
                    <option value="sold">Продадени</option>
                    <option value="dead">Умрели</option>
                    <option value="all">Всички</option>
                </SelectInput>
            </div>

            <AnimalList animals={animals.data} />
            <Pagination paginator={animals} />
        </AppLayout>
    );
}
