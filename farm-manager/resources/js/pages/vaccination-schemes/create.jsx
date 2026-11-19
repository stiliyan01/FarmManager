import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import SchemeForm from '@/components/vaccination-scheme/scheme-form';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function VaccinationSchemeCreate({ species, medications }) {
    const { data, setData, post, processing, errors } = useForm({
        species_id: '',
        name: '',
        medication_id: '',
        interval_days: 365,
        mandatory: true,
    });

    function submit(event) {
        event.preventDefault();
        post(route('vaccination-schemes.store'));
    }

    return (
        <AppLayout>
            <Head title="Нова схема" />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('vaccination-schemes.index')} className="text-primary">
                    Ваксинационни схеми
                </Link>{' '}
                / Нова схема
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">Нова схема</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <SchemeForm data={data} setData={setData} errors={errors} species={species} medications={medications} />

                    <div className="flex flex-wrap gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Добави
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={route('vaccination-schemes.index')}>Откажи</Link>
                        </Button>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
