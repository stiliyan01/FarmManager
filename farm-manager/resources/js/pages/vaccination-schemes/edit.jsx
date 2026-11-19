import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import SchemeForm from '@/components/vaccination-scheme/scheme-form';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router, useForm } from '@inertiajs/react';

export default function VaccinationSchemeEdit({ scheme, species, medications }) {
    const { data, setData, put, processing, errors } = useForm({
        species_id: scheme.species_id,
        name: scheme.name,
        medication_id: scheme.medication_id,
        interval_days: scheme.interval_days,
        mandatory: scheme.mandatory,
    });

    function submit(event) {
        event.preventDefault();
        put(route('vaccination-schemes.update', scheme.id));
    }

    function deleteScheme() {
        if (confirm(`Да изтрия ли схемата „${scheme.name}“?`)) {
            router.delete(route('vaccination-schemes.destroy', scheme.id));
        }
    }

    return (
        <AppLayout>
            <Head title={scheme.name} />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('vaccination-schemes.index')} className="text-primary">
                    Ваксинационни схеми
                </Link>{' '}
                / {scheme.name}
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">Схема „{scheme.name}“</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <SchemeForm data={data} setData={setData} errors={errors} species={species} medications={medications} />

                    <div className="flex flex-wrap justify-between gap-2.5">
                        <Button type="button" variant="outline" onClick={deleteScheme} className="text-red-700">
                            Изтрий схемата
                        </Button>

                        <div className="flex flex-wrap gap-2.5">
                            <Button variant="outline" asChild>
                                <Link href={route('vaccination-schemes.index')}>Откажи</Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Запази
                            </Button>
                        </div>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
