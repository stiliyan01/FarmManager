import MedicationForm from '@/components/medication/medication-form';
import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function MedicationEdit({ medication }) {
    const { data, setData, put, processing, errors } = useForm({
        name: medication.name,
        active_substance: medication.active_substance ?? '',
        withdrawal_milk_days: medication.withdrawal_milk_days,
        withdrawal_meat_days: medication.withdrawal_meat_days,
    });

    function submit(event) {
        event.preventDefault();
        put(route('medications.update', medication.id));
    }

    return (
        <AppLayout>
            <Head title={medication.name} />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('medications.index')} className="text-primary">
                    Препарати
                </Link>{' '}
                / {medication.name}
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">{medication.name}</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <MedicationForm data={data} setData={setData} errors={errors} />

                    <div className="flex flex-wrap gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Запази
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={route('medications.index')}>Откажи</Link>
                        </Button>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
