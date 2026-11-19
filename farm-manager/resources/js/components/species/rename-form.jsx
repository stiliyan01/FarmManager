import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/react';

// Малка форма за преименуване на място – на вид или на порода.
export default function RenameForm({ url, value, onDone }) {
    const { data, setData, put, processing, errors } = useForm({ name: value });

    function submit(event) {
        event.preventDefault();
        put(url, {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    }

    return (
        <form onSubmit={submit} className="flex flex-wrap items-start gap-2">
            <div className="min-w-0 flex-1 basis-40">
                <Input value={data.name} onChange={(event) => setData('name', event.target.value)} aria-label="Ново име" autoFocus />
                <InputError message={errors.name} className="mt-1" />
            </div>
            <Button type="submit" disabled={processing}>
                Запази
            </Button>
            <Button type="button" variant="outline" onClick={onDone}>
                Откажи
            </Button>
        </form>
    );
}
