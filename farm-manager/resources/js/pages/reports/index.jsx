import FormField from '@/components/form-field';
import PageHeader from '@/components/page-header';
import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

// Отчетите на фермата за изтегляне – Excel или PDF.
// Връзките са обикновени <a>, защото отговорът е файл, а не страница.
export default function ReportIndex({ farm, defaultFrom, defaultTo }) {
    const { errors } = usePage().props;
    const [from, setFrom] = useState(defaultFrom);
    const [to, setTo] = useState(defaultTo);

    return (
        <AppLayout farm={farm}>
            <Head title="Отчети" />

            <PageHeader title="Отчети" subtitle={farm.name} />

            <div className="grid items-start gap-4 lg:grid-cols-2">
                <Panel title="Ветеринарни записи за период">
                    <p className="text-muted-foreground mb-4">Всички ваксинации, лечения, прегледи и изследвания – с препарата и карантинния срок.</p>

                    <div className="mb-4 grid gap-4 sm:grid-cols-2">
                        <FormField label="От дата" htmlFor="from" error={errors.from}>
                            <Input id="from" type="date" value={from} onChange={(event) => setFrom(event.target.value)} />
                        </FormField>
                        <FormField label="До дата" htmlFor="to" error={errors.to}>
                            <Input id="to" type="date" value={to} onChange={(event) => setTo(event.target.value)} />
                        </FormField>
                    </div>

                    <div className="flex flex-wrap gap-2.5">
                        <Button asChild>
                            <a href={route('farms.reports.vet-events', { farm: farm.id, format: 'excel', from: from, to: to })}>Изтегли Excel</a>
                        </Button>
                        <Button variant="outline" asChild>
                            <a href={route('farms.reports.vet-events', { farm: farm.id, format: 'pdf', from: from, to: to })}>Изтегли PDF</a>
                        </Button>
                    </div>
                </Panel>

                <Panel title="Регистър на животните">
                    <p className="text-muted-foreground mb-4">
                        Всички животни във фермата – активни, продадени и умрели, с майката, групата и карантината към днешна дата.
                    </p>

                    <div className="flex flex-wrap gap-2.5">
                        <Button asChild>
                            <a href={route('farms.reports.animals', { farm: farm.id, format: 'excel' })}>Изтегли Excel</a>
                        </Button>
                        <Button variant="outline" asChild>
                            <a href={route('farms.reports.animals', { farm: farm.id, format: 'pdf' })}>Изтегли PDF</a>
                        </Button>
                    </div>
                </Panel>
            </div>
        </AppLayout>
    );
}
