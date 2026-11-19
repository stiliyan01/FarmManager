import FormField from '@/components/form-field';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

// Регистрация на нов животновъден обект от ОДБХ.
export default function FarmCreate({ regions }) {
    const { data, setData, post, processing, errors } = useForm({
        region_id: '',
        reg_number: '',
        name: '',
        settlement: '',
        capacity: '',
        owner_email: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('farms.store'));
    }

    return (
        <AppLayout>
            <Head title="Регистрация на обект" />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('farms.index')} className="text-primary">
                    Ферми
                </Link>{' '}
                / Нов обект
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">Регистрация на обект</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    {/* Областта избира само администраторът – регистраторът работи в своята */}
                    {regions.length > 0 && (
                        <FormField label="Област *" htmlFor="region_id" error={errors.region_id}>
                            <SelectInput id="region_id" value={data.region_id} onChange={(event) => setData('region_id', event.target.value)}>
                                <option value="">– изберете –</option>
                                {regions.map((region) => (
                                    <option key={region.id} value={region.id}>
                                        {region.name}
                                    </option>
                                ))}
                            </SelectInput>
                        </FormField>
                    )}

                    <div className="grid gap-5 sm:grid-cols-2">
                        <FormField label="Рег. № *" htmlFor="reg_number" error={errors.reg_number}>
                            <Input
                                id="reg_number"
                                value={data.reg_number}
                                onChange={(event) => setData('reg_number', event.target.value)}
                                placeholder="напр. 9700-0130"
                            />
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
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(event) => setData('name', event.target.value)}
                            placeholder="напр. Ферма Лозево"
                        />
                    </FormField>

                    <FormField label="Населено място *" htmlFor="settlement" error={errors.settlement}>
                        <Input
                            id="settlement"
                            value={data.settlement}
                            onChange={(event) => setData('settlement', event.target.value)}
                            placeholder="напр. с. Лозево"
                        />
                    </FormField>

                    <FormField label="Имейл на собственика" htmlFor="owner_email" error={errors.owner_email}>
                        <Input
                            id="owner_email"
                            type="email"
                            value={data.owner_email}
                            onChange={(event) => setData('owner_email', event.target.value)}
                        />
                        <p className="text-muted-foreground text-[13px]">
                            Собственикът получава покана по имейл и сам създава акаунта си. Може да се изпрати и по-късно.
                        </p>
                    </FormField>

                    <div className="flex flex-wrap gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Регистрирай
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={route('farms.index')}>Откажи</Link>
                        </Button>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
