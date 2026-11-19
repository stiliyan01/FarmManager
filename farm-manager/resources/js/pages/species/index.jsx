import InputError from '@/components/input-error';
import PageHeader from '@/components/page-header';
import Panel from '@/components/panel';
import SpeciesCard from '@/components/species/species-card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { Head, useForm } from '@inertiajs/react';

// Номенклатурата „Видове и породи“.
export default function SpeciesIndex({ species }) {
    const { data, setData, post, processing, errors, reset } = useForm({ name: '' });

    function submit(event) {
        event.preventDefault();
        post(route('species.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <AppLayout>
            <Head title="Видове и породи" />

            <PageHeader title="Видове и породи" subtitle="Номенклатура – избира се при добавяне на животно" />

            <Panel title="Нов вид" className="mb-4 max-w-xl">
                <form onSubmit={submit} className="flex items-start gap-2">
                    <div className="min-w-0 flex-1">
                        <Input
                            value={data.name}
                            onChange={(event) => setData('name', event.target.value)}
                            placeholder="напр. Бивол"
                            aria-label="Име на вида"
                        />
                        <InputError message={errors.name} className="mt-1" />
                    </div>
                    <Button type="submit" disabled={processing}>
                        Добави
                    </Button>
                </form>
            </Panel>

            <div className="grid items-start gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                {species.map((item) => (
                    <SpeciesCard key={item.id} species={item} />
                ))}
            </div>
        </AppLayout>
    );
}
