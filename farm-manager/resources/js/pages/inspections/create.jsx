import FormField from '@/components/form-field';
import InputError from '@/components/input-error';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

// Нова проверка във ферма от областта на инспектора.
export default function InspectionCreate({ farms, results, selectedFarmId }) {
    const { data, setData, post, processing, errors } = useForm({
        farm_id: selectedFarmId ?? '',
        inspected_at: new Date().toISOString().slice(0, 10), // днешна дата
        result: 'ok',
        findings: '',
    });

    function submit(event) {
        event.preventDefault();
        post(route('inspections.store'));
    }

    return (
        <AppLayout>
            <Head title="Нова проверка" />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('inspections.index')} className="text-primary">
                    Проверки
                </Link>{' '}
                / Нова проверка
            </div>

            <h1 className="mb-6 text-2xl font-bold tracking-tight md:text-[28px]">Нова проверка</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <div className="grid gap-5 sm:grid-cols-2">
                        <FormField label="Ферма *" htmlFor="farm_id" error={errors.farm_id}>
                            <SelectInput id="farm_id" value={data.farm_id} onChange={(event) => setData('farm_id', event.target.value)}>
                                <option value="">– изберете –</option>
                                {farms.map((farm) => (
                                    <option key={farm.id} value={farm.id}>
                                        {farm.name} · {farm.reg_number}
                                    </option>
                                ))}
                            </SelectInput>
                        </FormField>

                        <FormField label="Дата на проверката *" htmlFor="inspected_at" error={errors.inspected_at}>
                            <Input
                                id="inspected_at"
                                type="date"
                                value={data.inspected_at}
                                onChange={(event) => setData('inspected_at', event.target.value)}
                            />
                        </FormField>
                    </div>

                    <fieldset>
                        <legend className="mb-2 text-sm font-medium">Резултат *</legend>
                        <div className="flex flex-wrap gap-2.5">
                            {results.map((result) => (
                                <label
                                    key={result.value}
                                    className={
                                        data.result === result.value
                                            ? 'border-primary bg-accent flex h-11 cursor-pointer items-center gap-2 rounded-md border px-4 font-semibold'
                                            : 'border-input flex h-11 cursor-pointer items-center gap-2 rounded-md border bg-white px-4'
                                    }
                                >
                                    <input
                                        type="radio"
                                        name="result"
                                        value={result.value}
                                        checked={data.result === result.value}
                                        onChange={(event) => setData('result', event.target.value)}
                                        className="accent-primary"
                                    />
                                    {result.label}
                                </label>
                            ))}
                        </div>
                        <InputError message={errors.result} className="mt-2" />
                    </fieldset>

                    <FormField label={data.result === 'violation' ? 'Констатации *' : 'Констатации'} htmlFor="findings" error={errors.findings}>
                        <Textarea
                            id="findings"
                            rows={4}
                            value={data.findings}
                            onChange={(event) => setData('findings', event.target.value)}
                            placeholder="Какво е проверено и какво е установено"
                        />
                    </FormField>

                    <div className="flex flex-wrap gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Запиши проверката
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={route('inspections.index')}>Откажи</Link>
                        </Button>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
