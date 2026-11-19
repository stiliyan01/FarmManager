import FormField from '@/components/form-field';
import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import MedicationFields from '@/components/vet-event/medication-fields';
import TypePicker from '@/components/vet-event/type-picker';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router, useForm } from '@inertiajs/react';

// Корекция на запис. Старите и новите стойности остават в журнала.
export default function VetEventEdit({ farm, vetEvent, medications, types }) {
    const { data, setData, put, processing, errors } = useForm({
        type: vetEvent.type,
        event_date: vetEvent.event_date,
        medication_id: vetEvent.medication_id ?? '',
        dose: vetEvent.dose ?? '',
        diagnosis: vetEvent.diagnosis ?? '',
        notes: vetEvent.notes ?? '',
    });

    const needsMedication = data.type === 'vaccination' || data.type === 'treatment';

    function changeType(type) {
        if (type === 'exam' || type === 'test') {
            setData({ ...data, type: type, medication_id: '', dose: '' });
        } else {
            setData('type', type);
        }
    }

    function submit(event) {
        event.preventDefault();
        put(route('vet-events.update', vetEvent.id));
    }

    function deleteEvent() {
        if (confirm('Да изтрия ли този запис? Изтриването също остава в журнала.')) {
            router.delete(route('vet-events.destroy', vetEvent.id));
        }
    }

    return (
        <AppLayout farm={farm}>
            <Head title="Редакция на запис" />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('animals.show', vetEvent.animal.id)} className="text-primary">
                    {vetEvent.animal.ear_tag}
                </Link>
                {' / '}
                Редакция на запис
            </div>

            <Panel className="max-w-3xl">
                <h1 className="text-2xl font-bold tracking-tight">Редакция на запис</h1>
                <p className="text-muted-foreground mt-1 mb-6">
                    Животно {vetEvent.animal.ear_tag}. Всяка промяна се записва в журнала – старата и новата стойност.
                </p>

                <form onSubmit={submit} className="grid gap-6">
                    <TypePicker types={types} value={data.type} onChange={changeType} error={errors.type} />

                    <FormField label="Дата *" htmlFor="event_date" error={errors.event_date}>
                        <Input
                            id="event_date"
                            type="date"
                            value={data.event_date}
                            onChange={(event) => setData('event_date', event.target.value)}
                            className="sm:w-56"
                        />
                    </FormField>

                    {needsMedication && <MedicationFields medications={medications} data={data} setData={setData} errors={errors} />}

                    <FormField label="Диагноза / резултат" htmlFor="diagnosis" error={errors.diagnosis}>
                        <Input id="diagnosis" value={data.diagnosis} onChange={(event) => setData('diagnosis', event.target.value)} />
                    </FormField>

                    <FormField label="Бележка" htmlFor="notes" error={errors.notes}>
                        <Textarea id="notes" rows={3} value={data.notes} onChange={(event) => setData('notes', event.target.value)} />
                    </FormField>

                    <div className="flex flex-wrap justify-between gap-2.5">
                        <Button type="button" variant="outline" onClick={deleteEvent} className="text-red-700">
                            Изтрий записа
                        </Button>

                        <div className="flex flex-wrap gap-2.5">
                            <Button variant="outline" asChild>
                                <Link href={route('animals.show', vetEvent.animal.id)}>Откажи</Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Запази промените
                            </Button>
                        </div>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
