import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import SearchForm from '@/components/search-form';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';

// Препаратите и карантинните им срокове. Ветеринарят ги гледа за справка, админът ги поддържа.
export default function MedicationIndex({ medications, filters, canManage }) {
    function search(text) {
        router.get(route('medications.index'), { search: text }, { preserveState: true });
    }

    return (
        <AppLayout>
            <Head title="Препарати" />

            <PageHeader title="Препарати" subtitle="Карантинен срок след прилагане – за мляко и за месо">
                {canManage && (
                    <Button asChild>
                        <Link href={route('medications.create')}>+ Нов препарат</Link>
                    </Button>
                )}
            </PageHeader>

            <div className="mb-4">
                <SearchForm value={filters.search} placeholder="Име или активно вещество" onSearch={search} />
            </div>

            {medications.data.length === 0 && <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Няма такъв препарат.</p>}

            {medications.data.length > 0 && (
                <>
                    <div className="flex flex-col gap-2.5 lg:hidden">
                        {medications.data.map((medication) => (
                            <div key={medication.id} className="bg-card rounded-lg border px-4 py-3">
                                <div className="flex items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <div className="font-semibold">{medication.name}</div>
                                        <div className="text-muted-foreground text-[13px]">{medication.active_substance ?? '—'}</div>
                                    </div>
                                    {canManage && (
                                        <Link href={route('medications.edit', medication.id)} className="text-primary text-[13px] font-medium">
                                            Редакция
                                        </Link>
                                    )}
                                </div>
                                <div className="mt-1.5 text-[13px] text-gray-700">
                                    Мляко: {medication.withdrawal_milk_days} дни · Месо: {medication.withdrawal_meat_days} дни
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                        <table className="w-full">
                            <thead>
                                <tr className="text-muted-foreground border-b text-left text-xs">
                                    <th className="px-4 py-3 font-medium">Препарат</th>
                                    <th className="px-4 py-3 font-medium">Активно вещество</th>
                                    <th className="px-4 py-3 font-medium">Мляко (дни)</th>
                                    <th className="px-4 py-3 font-medium">Месо (дни)</th>
                                    {canManage && (
                                        <th className="px-4 py-3 font-medium">
                                            <span className="sr-only">Действия</span>
                                        </th>
                                    )}
                                </tr>
                            </thead>
                            <tbody>
                                {medications.data.map((medication) => (
                                    <tr key={medication.id} className="border-b last:border-0">
                                        <td className="px-4 py-3 font-semibold">{medication.name}</td>
                                        <td className="px-4 py-3 text-gray-700">{medication.active_substance ?? '—'}</td>
                                        <td className="px-4 py-3">{medication.withdrawal_milk_days}</td>
                                        <td className="px-4 py-3">{medication.withdrawal_meat_days}</td>
                                        {canManage && (
                                            <td className="px-4 py-3 text-right">
                                                <Link href={route('medications.edit', medication.id)} className="text-primary font-medium">
                                                    Редакция
                                                </Link>
                                            </td>
                                        )}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </>
            )}

            <Pagination paginator={medications} />
        </AppLayout>
    );
}
