import FormField from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/react';

// Разделът „Тегло“: формуляр за ново измерване и списък с досегашните.
export default function WeightList({ animal, weights, canEdit }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        weight_kg: '',
        measured_at: new Date().toISOString().slice(0, 10), // днешна дата
    });

    function submit(event) {
        event.preventDefault();
        post(route('animals.weights.store', animal.id), {
            preserveScroll: true,
            onSuccess: () => reset('weight_kg'),
        });
    }

    return (
        <div className="mt-4">
            {canEdit && animal.is_active && (
                <form onSubmit={submit} className="mb-5 flex flex-wrap items-end gap-2.5">
                    <FormField label="Тегло (кг)" htmlFor="weight_kg" error={errors.weight_kg}>
                        <Input
                            id="weight_kg"
                            type="number"
                            step="0.1"
                            value={data.weight_kg}
                            onChange={(event) => setData('weight_kg', event.target.value)}
                            className="w-32"
                        />
                    </FormField>
                    <FormField label="Дата" htmlFor="measured_at" error={errors.measured_at}>
                        <Input
                            id="measured_at"
                            type="date"
                            value={data.measured_at}
                            onChange={(event) => setData('measured_at', event.target.value)}
                        />
                    </FormField>
                    <Button type="submit" disabled={processing}>
                        Запиши тегло
                    </Button>
                </form>
            )}

            {weights.length === 0 && <p className="text-muted-foreground">Няма измервания.</p>}

            {weights.length > 0 && (
                <table className="w-full max-w-sm">
                    <thead>
                        <tr className="text-muted-foreground border-b text-left text-xs">
                            <th className="py-2.5 font-medium">Дата</th>
                            <th className="py-2.5 font-medium">Тегло</th>
                        </tr>
                    </thead>
                    <tbody>
                        {weights.map((weight) => (
                            <tr key={weight.id} className="border-b last:border-0">
                                <td className="py-2.5 text-gray-700">{weight.date}</td>
                                <td className="py-2.5 font-medium">{weight.weight_kg} кг</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}
        </div>
    );
}
