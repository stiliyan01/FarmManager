import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

// Журналът на действията – кой, кога, какво е създал, редактирал или изтрил.
export default function ActivityLogIndex({ activities }) {
    return (
        <AppLayout>
            <Head title="Журнал" />

            <PageHeader title="Журнал на действията" subtitle="Ветеринарни записи, животни, ферми, назначения и проверки" />

            {activities.data.length === 0 && <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Журналът е празен.</p>}

            {activities.data.length > 0 && (
                <ul className="bg-card divide-y rounded-lg border">
                    {activities.data.map((activity) => (
                        <li key={activity.id} className="px-4 py-3 sm:px-5">
                            <div className="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                <div>
                                    <span className="font-semibold">{activity.user}</span> {activity.action}
                                </div>
                                <span className="text-muted-foreground text-[13px] whitespace-nowrap">{activity.date}</span>
                            </div>

                            {activity.subject && <div className="text-muted-foreground mt-0.5 text-[13px]">{activity.subject}</div>}

                            {activity.changes.length > 0 && (
                                <ul className="mt-2 flex flex-col gap-1 text-[13px]">
                                    {activity.changes.map((change) => (
                                        <li key={change.field} className="bg-muted/60 rounded-md px-2.5 py-1.5">
                                            <span className="text-muted-foreground">{change.field}:</span>{' '}
                                            <span className="text-red-800 line-through decoration-red-300">{change.old}</span> →{' '}
                                            <span className="font-medium text-green-800">{change.new}</span>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </li>
                    ))}
                </ul>
            )}

            <Pagination paginator={activities} />
        </AppLayout>
    );
}
