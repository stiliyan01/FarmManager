import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import Pill from '@/components/pill';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { inspectionResultColors } from '@/lib/colors';
import { Head, Link, router } from '@inertiajs/react';

// Проверките на ОДБХ – най-новите са най-горе.
export default function InspectionIndex({ inspections, filters, canCreate }) {
    function changeResult(event) {
        router.get(route('inspections.index'), { result: event.target.value }, { preserveState: true });
    }

    return (
        <AppLayout>
            <Head title="Проверки" />

            <PageHeader title="Проверки" subtitle="Проверки на ОДБХ по фермите">
                {canCreate && (
                    <Button asChild>
                        <Link href={route('inspections.create')}>+ Нова проверка</Link>
                    </Button>
                )}
            </PageHeader>

            <div className="mb-4">
                <SelectInput value={filters.result} onChange={changeResult} aria-label="Резултат" className="sm:w-56">
                    <option value="all">Всички резултати</option>
                    <option value="ok">Без нарушения</option>
                    <option value="violation">С нарушение</option>
                </SelectInput>
            </div>

            {inspections.data.length === 0 && <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Няма проверки.</p>}

            {inspections.data.length > 0 && (
                <>
                    <div className="flex flex-col gap-2.5 lg:hidden">
                        {inspections.data.map((inspection) => (
                            <div key={inspection.id} className="bg-card rounded-lg border px-4 py-3">
                                <div className="flex items-start justify-between gap-3">
                                    <Link href={route('farms.show', inspection.farm_id)} className="text-primary min-w-0 font-semibold">
                                        {inspection.farm}
                                    </Link>
                                    <Pill color={inspectionResultColors[inspection.result]}>{inspection.result_label}</Pill>
                                </div>
                                {inspection.findings && <p className="mt-1.5 text-gray-700">{inspection.findings}</p>}
                                <div className="text-muted-foreground mt-1.5 text-[13px]">
                                    {inspection.date} · {inspection.inspector}
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                        <table className="w-full">
                            <thead>
                                <tr className="text-muted-foreground border-b text-left text-xs">
                                    <th className="px-4 py-3 font-medium">Дата</th>
                                    <th className="px-4 py-3 font-medium">Ферма</th>
                                    <th className="px-4 py-3 font-medium">Инспектор</th>
                                    <th className="px-4 py-3 font-medium">Резултат</th>
                                    <th className="px-4 py-3 font-medium">Констатации</th>
                                </tr>
                            </thead>
                            <tbody>
                                {inspections.data.map((inspection) => (
                                    <tr key={inspection.id} className="border-b align-top last:border-0">
                                        <td className="px-4 py-3 whitespace-nowrap text-gray-700">{inspection.date}</td>
                                        <td className="px-4 py-3">
                                            <Link href={route('farms.show', inspection.farm_id)} className="text-primary font-semibold">
                                                {inspection.farm}
                                            </Link>
                                            <div className="text-muted-foreground text-xs">{inspection.reg_number}</div>
                                        </td>
                                        <td className="px-4 py-3 text-gray-700">{inspection.inspector}</td>
                                        <td className="px-4 py-3">
                                            <Pill color={inspectionResultColors[inspection.result]}>{inspection.result_label}</Pill>
                                        </td>
                                        <td className="px-4 py-3 text-gray-700">{inspection.findings ?? '—'}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </>
            )}

            <Pagination paginator={inspections} />
        </AppLayout>
    );
}
