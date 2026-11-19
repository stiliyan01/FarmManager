import FarmList from '@/components/farm/farm-list';
import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import SearchForm from '@/components/search-form';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';

const filterButtons = [
    { value: 'all', label: 'Всички' },
    { value: 'without_vet', label: 'Без ветеринар' },
    { value: 'without_owner', label: 'Без собственик' },
];

// Животновъдните обекти – за регистратора и инспектора в тяхната област, за админа – всички.
export default function FarmIndex({ farms, farmsCount, regionName, filters, canCreate, canManage }) {
    function applyFilters(changes) {
        router.get(route('farms.index'), { ...filters, ...changes }, { preserveState: true });
    }

    let subtitle = `Всички области · ${farmsCount} обекта`;
    if (regionName) {
        subtitle = `Област ${regionName} · ${farmsCount} регистрирани обекта`;
    }

    return (
        <AppLayout>
            <Head title="Ферми" />

            <PageHeader title="Животновъдни обекти" subtitle={subtitle}>
                {canCreate && (
                    <Button asChild>
                        <Link href={route('farms.create')}>+ Регистрирай обект</Link>
                    </Button>
                )}
            </PageHeader>

            <div className="mb-4 flex flex-wrap items-center gap-2.5">
                <SearchForm value={filters.search} placeholder="Рег. №, име или място" onSearch={(search) => applyFilters({ search })} />

                <div className="flex flex-wrap gap-2">
                    {filterButtons.map((button) => (
                        <button
                            key={button.value}
                            type="button"
                            aria-pressed={filters.filter === button.value}
                            onClick={() => applyFilters({ filter: button.value })}
                            className={
                                filters.filter === button.value
                                    ? 'bg-primary border-primary h-10 rounded-full border px-4 text-[13px] font-medium text-white'
                                    : 'border-input h-10 rounded-full border bg-white px-4 text-[13px] font-medium text-gray-700'
                            }
                        >
                            {button.label}
                        </button>
                    ))}
                </div>
            </div>

            <FarmList farms={farms.data} canManage={canManage} />
            <Pagination paginator={farms} />
        </AppLayout>
    );
}
