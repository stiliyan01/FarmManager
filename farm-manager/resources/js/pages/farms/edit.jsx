import OwnerPanel from '@/components/farm/owner-panel';
import VetAssignmentPanel from '@/components/farm/vet-assignment-panel';
import FormField from '@/components/form-field';
import PageHeader from '@/components/page-header';
import Panel from '@/components/panel';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

// Управление на обекта от регистратора: данни, собственик и обслужващ ветеринар.
export default function FarmEdit({ farm, owner, invitations, currentVet, assignments, vets }) {
    const { data, setData, put, processing, errors } = useForm({
        reg_number: farm.reg_number,
        name: farm.name,
        settlement: farm.settlement,
        capacity: farm.capacity ?? '',
    });

    function submit(event) {
        event.preventDefault();
        put(route('farms.update', farm.id), { preserveScroll: true });
    }

    return (
        <AppLayout farm={farm}>
            <Head title={farm.name} />

            <PageHeader title={farm.name} subtitle={`Рег. № ${farm.reg_number} · обл. ${farm.region}`}>
                <Button variant="outline" asChild>
                    <Link href={route('farms.show', farm.id)}>Табло на фермата</Link>
                </Button>
            </PageHeader>

            <div className="grid items-start gap-4 lg:grid-cols-2">
                <Panel title="Данни за обекта">
                    <form onSubmit={submit} className="grid gap-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <FormField label="Рег. № *" htmlFor="reg_number" error={errors.reg_number}>
                                <Input id="reg_number" value={data.reg_number} onChange={(event) => setData('reg_number', event.target.value)} />
                            </FormField>

                            <FormField label="Капацитет (животни)" htmlFor="capacity" error={errors.capacity}>
                                <Input
                                    id="capacity"
                                    type="number"
                                    min="1"
                                    value={data.capacity}
                                    onChange={(event) => setData('capacity', event.target.value)}
                                />
                            </FormField>
                        </div>

                        <FormField label="Име на обекта *" htmlFor="name" error={errors.name}>
                            <Input id="name" value={data.name} onChange={(event) => setData('name', event.target.value)} />
                        </FormField>

                        <FormField label="Населено място *" htmlFor="settlement" error={errors.settlement}>
                            <Input id="settlement" value={data.settlement} onChange={(event) => setData('settlement', event.target.value)} />
                        </FormField>

                        <div>
                            <Button type="submit" disabled={processing}>
                                Запази данните
                            </Button>
                        </div>
                    </form>
                </Panel>

                <div className="grid gap-4">
                    <OwnerPanel farm={farm} owner={owner} invitations={invitations} />
                    <VetAssignmentPanel farm={farm} currentVet={currentVet} assignments={assignments} vets={vets} />
                </div>
            </div>
        </AppLayout>
    );
}
