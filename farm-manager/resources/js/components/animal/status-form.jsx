import FormField from '@/components/form-field';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/react';

// Смяна на статус: продадено или умряло.
export default function StatusForm({ animal, onClose }) {
    const { data, setData, put, processing, errors } = useForm({
        status: 'sold',
        status_date: new Date().toISOString().slice(0, 10), // днешна дата
    });

    function submit(event) {
        event.preventDefault();
        put(route('animals.status.update', animal.id), {
            preserveScroll: true,
            onSuccess: () => onClose(),
        });
    }

    return (
        <Panel title="Промяна на статуса" className="mb-5">
            <form onSubmit={submit} className="flex flex-wrap items-end gap-2.5">
                <FormField label="Нов статус" htmlFor="status" error={errors.status}>
                    <SelectInput id="status" value={data.status} onChange={(event) => setData('status', event.target.value)} className="w-44">
                        <option value="sold">Продадено</option>
                        <option value="dead">Умряло</option>
                    </SelectInput>
                </FormField>

                <FormField label="Дата" htmlFor="status_date" error={errors.status_date}>
                    <Input id="status_date" type="date" value={data.status_date} onChange={(event) => setData('status_date', event.target.value)} />
                </FormField>

                <Button type="submit" disabled={processing}>
                    Запази
                </Button>
                <Button type="button" variant="outline" onClick={onClose}>
                    Откажи
                </Button>
            </form>
        </Panel>
    );
}
