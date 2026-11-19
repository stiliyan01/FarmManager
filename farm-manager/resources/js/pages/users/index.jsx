import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import Panel from '@/components/panel';
import SearchForm from '@/components/search-form';
import { Button } from '@/components/ui/button';
import UserList from '@/components/user/user-list';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';

// Потребители и роли – началната страница на администратора.
export default function UserIndex({ users, usersCount, roleCounts, nomenclatures, filters }) {
    function applyFilters(changes) {
        router.get(route('users.index'), { ...filters, ...changes }, { preserveState: true });
    }

    // Второ натискане на избраната роля маха филтъра.
    function toggleRole(role) {
        if (filters.role === role) {
            applyFilters({ role: 'all' });
        } else {
            applyFilters({ role: role });
        }
    }

    return (
        <AppLayout>
            <Head title="Потребители" />

            <PageHeader title="Потребители и роли" subtitle={`Цялата система · ${usersCount} потребители`}>
                <Button asChild>
                    <Link href={route('users.create')}>+ Нов потребител</Link>
                </Button>
            </PageHeader>

            <section aria-label="Брой потребители по роли" className="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5">
                {roleCounts.map((role) => (
                    <button
                        key={role.value}
                        type="button"
                        aria-pressed={filters.role === role.value}
                        onClick={() => toggleRole(role.value)}
                        className={
                            filters.role === role.value
                                ? 'border-primary rounded-lg border-2 bg-white px-4 py-3 text-left'
                                : 'border-border rounded-lg border-2 bg-white px-4 py-3 text-left'
                        }
                    >
                        <div className="text-muted-foreground text-[13px]">{role.label}</div>
                        <div className="mt-1 text-[26px] font-bold">{role.count}</div>
                    </button>
                ))}
            </section>

            <div className="grid items-start gap-4 xl:grid-cols-4">
                <div className="min-w-0 xl:col-span-3">
                    <div className="mb-4">
                        <SearchForm value={filters.search} placeholder="Търси по име или имейл" onSearch={(search) => applyFilters({ search })} />
                    </div>

                    <UserList users={users.data} />
                    <Pagination paginator={users} />
                </div>

                <Panel title="Номенклатури">
                    <div className="grid grid-cols-2 gap-2.5">
                        <div className="bg-muted/60 rounded-lg p-3">
                            <div className="text-lg font-bold">{nomenclatures.regions}</div>
                            <div className="text-muted-foreground text-[13px]">области</div>
                        </div>
                        <Link href={route('species.index')} className="bg-muted/60 hover:bg-muted rounded-lg p-3">
                            <div className="text-lg font-bold">{nomenclatures.species}</div>
                            <div className="text-muted-foreground text-[13px]">вида животни</div>
                        </Link>
                        <Link href={route('species.index')} className="bg-muted/60 hover:bg-muted rounded-lg p-3">
                            <div className="text-lg font-bold">{nomenclatures.breeds}</div>
                            <div className="text-muted-foreground text-[13px]">породи</div>
                        </Link>
                        <Link href={route('medications.index')} className="bg-muted/60 hover:bg-muted rounded-lg p-3">
                            <div className="text-lg font-bold">{nomenclatures.medications}</div>
                            <div className="text-muted-foreground text-[13px]">препарата</div>
                        </Link>
                    </div>
                </Panel>
            </div>
        </AppLayout>
    );
}
