import FormField from '@/components/form-field';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/react';

// Назначаване и смяна на обслужващия ветеринар + историята на назначенията.
export default function VetAssignmentPanel({ farm, currentVet, assignments, vets }) {
    const { data, setData, post, processing, errors } = useForm({
        vet_id: '',
        valid_from: new Date().toISOString().slice(0, 10), // днешна дата
    });

    function submit(event) {
        event.preventDefault();
        post(route('farms.vets.store', farm.id), {
            preserveScroll: true,
            onSuccess: () => setData('vet_id', ''),
        });
    }

    return (
        <Panel title="Обслужващ ветеринар">
            {currentVet && (
                <div className="mb-4">
                    <div className="font-semibold">{currentVet.name}</div>
                    <div className="text-muted-foreground mt-0.5 text-[13px]">назначен от {currentVet.since}</div>
                </div>
            )}

            {!currentVet && <p className="mb-4 font-semibold text-red-800">Няма назначен ветеринар.</p>}

            <form onSubmit={submit} className="grid gap-4">
                <div className="grid gap-4 sm:grid-cols-2">
                    <FormField label={currentVet ? 'Нов ветеринар' : 'Ветеринар'} htmlFor="vet_id" error={errors.vet_id}>
                        <SelectInput id="vet_id" value={data.vet_id} onChange={(event) => setData('vet_id', event.target.value)}>
                            <option value="">– изберете –</option>
                            {vets.map((vet) => (
                                <option key={vet.id} value={vet.id}>
                                    {vet.name}
                                </option>
                            ))}
                        </SelectInput>
                    </FormField>

                    <FormField label="В сила от" htmlFor="valid_from" error={errors.valid_from}>
                        <Input id="valid_from" type="date" value={data.valid_from} onChange={(event) => setData('valid_from', event.target.value)} />
                    </FormField>
                </div>

                {currentVet && (
                    <p className="text-muted-foreground text-[13px]">
                        Сегашното назначение се затваря с предходния ден. {currentVet.name} губи достъп до фермата, а записите му остават.
                    </p>
                )}

                <div>
                    <Button type="submit" disabled={processing}>
                        {currentVet ? 'Смени ветеринаря' : 'Назначи ветеринар'}
                    </Button>
                </div>
            </form>

            {assignments.length > 0 && (
                <div className="mt-5 border-t pt-4">
                    <div className="mb-2 font-semibold">История на назначенията</div>
                    <ul className="flex flex-col gap-1.5 text-[13px] text-gray-700">
                        {assignments.map((assignment) => (
                            <li key={assignment.id}>
                                {assignment.vet} · {assignment.from} – {assignment.to ?? 'сега'}
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </Panel>
    );
}
