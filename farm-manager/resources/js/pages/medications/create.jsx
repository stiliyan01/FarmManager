import MedicationForm from '@/components/medication/medication-form';
import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function MedicationCreate() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        active_substance: '',
        withdrawal_milk_days: 0,
        withdrawal_meat_days: 0,
    });

    function submit(event) {
        event.preventDefault();
        post(route('medications.store'));
    }

    return (
        <AppLayout>
            <Head title="Нов препарат" />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('medications.index')} className="text-primary">
                    Препарати
                </Link>{' '}
                / Нов препарат
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">Нов препарат</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <MedicationForm data={data} setData={setData} errors={errors} />

                    <div className="flex flex-wrap gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Добави
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
