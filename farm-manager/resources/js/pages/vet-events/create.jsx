import FormField from '@/components/form-field';
import InputError from '@/components/input-error';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AnimalPicker from '@/components/vet-event/animal-picker';
import MedicationFields from '@/components/vet-event/medication-fields';
import TypePicker from '@/components/vet-event/type-picker';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router, useForm } from '@inertiajs/react';

// Нов ветеринарен запис – за едно животно или за цяла група (масов запис).
export default function VetEventCreate({ farm, myFarms, animals, medications, types, selectedAnimalId }) {
    const { data, setData, post, processing, errors } = useForm({
        type: 'vaccination',
        event_date: new Date().toISOString().slice(0, 10), // днешна дата
        animal_ids: selectedAnimalId ? [selectedAnimalId] : [],
        medication_id: '',
        dose: '',
        diagnosis: '',
        notes: '',
    });

    // Препарат има само при ваксинация и лечение.
    const needsMedication = data.type === 'vaccination' || data.type === 'treatment';

    // Грешка за някое от избраните животни идва като animal_ids.0, animal_ids.1 …
    let animalsError = errors.animal_ids;
    for (const key in errors) {
        if (key.startsWith('animal_ids.')) {
            animalsError = errors[key];
        }
    }

    let submitText = 'Запиши';
    if (data.animal_ids.length > 1) {
        submitText = `Запиши за ${data.animal_ids.length} животни`;
    }

    function changeType(type) {
        if (type === 'exam' || type === 'test') {
            setData({ ...data, type: type, medication_id: '', dose: '' });
        } else {
            setData('type', type);
        }
    }

    function switchFarm(event) {
        router.visit(route('farms.vet-events.create', event.target.value));
    }

    function submit(event) {
        event.preventDefault();
        post(route('farms.vet-events.store', farm.id));
    }

    return (
        <AppLayout farm={farm}>
            <Head title="Нов запис" />

            <Panel className="max-w-3xl">
                <h1 className="text-2xl font-bold tracking-tight">Нов ветеринарен запис</h1>
                <p className="text-muted-foreground mt-1 mb-6">Записът се вижда от собственика и от инспектора на ОДБХ.</p>

                <form onSubmit={submit} className="grid gap-6">
                    <TypePicker types={types} value={data.type} onChange={changeType} error={errors.type} />

                    <div className="grid gap-5 sm:grid-cols-2">
                        <FormField label="Ферма" htmlFor="farm">
                            <SelectInput id="farm" value={farm.id} onChange={switchFarm}>
                                {myFarms.map((myFarm) => (
                                    <option key={myFarm.id} value={myFarm.id}>
                                        {myFarm.name}
                                    </option>
                                ))}
                            </SelectInput>
                        </FormField>

                        <FormField label="Дата *" htmlFor="event_date" error={errors.event_date}>
                            <Input
                                id="event_date"
                                type="date"
                                value={data.event_date}
                                onChange={(event) => setData('event_date', event.target.value)}
                            />
                        </FormField>
                    </div>

                    <div className="grid gap-2">
                        <Label>Животни *</Label>
                        <AnimalPicker animals={animals} selectedIds={data.animal_ids} onChange={(ids) => setData('animal_ids', ids)} />
                        <InputError message={animalsError} />
                    </div>

                    {needsMedication && <MedicationFields medications={medications} data={data} setData={setData} errors={errors} />}

                    <FormField label="Диагноза / резултат" htmlFor="diagnosis" error={errors.diagnosis}>
                        <Input
                            id="diagnosis"
                            value={data.diagnosis}
                            onChange={(event) => setData('diagnosis', event.target.value)}
                            placeholder="напр. Пневмония"
                        />
                    </FormField>

                    <FormField label="Бележка" htmlFor="notes" error={errors.notes}>
                        <Textarea id="notes" rows={3} value={data.notes} onChange={(event) => setData('notes', event.target.value)} />
                    </FormField>

                    <div className="flex flex-wrap justify-end gap-2.5">
                        <Button variant="outline" asChild>
                            <Link href={route('farms.show', farm.id)}>Откажи</Link>
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {submitText}
                        </Button>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
