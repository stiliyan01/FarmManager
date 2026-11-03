import FormField from '@/components/form-field';
import Panel from '@/components/panel';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function AnimalCreate({ farm, species, mothers, motherId }) {
    // При „Регистрирай раждане“ майката е избрана предварително – взимаме вида от нея.
    const preselectedMother = mothers.find((mother) => mother.id === motherId);

    const { data, setData, post, processing, errors } = useForm({
        ear_tag: '',
        species_id: preselectedMother ? preselectedMother.species_id : '',
        breed_id: '',
        sex: 'female',
        birth_date: '',
        mother_id: preselectedMother ? preselectedMother.id : '',
        group_name: preselectedMother ? 'Приплод' : '',
        notes: '',
    });

    const selectedSpecies = species.find((item) => item.id === Number(data.species_id));
    const breeds = selectedSpecies ? selectedSpecies.breeds : [];
    const mothersOfSpecies = mothers.filter((mother) => mother.species_id === Number(data.species_id));

    // При смяна на вида породата и майката вече не важат.
    function changeSpecies(speciesId) {
        setData({ ...data, species_id: speciesId, breed_id: '', mother_id: '' });
    }

    function submit(event) {
        event.preventDefault();
        post(route('farms.animals.store', farm.id));
    }

    return (
        <AppLayout farm={farm}>
            <Head title="Добави животно" />

            <div className="text-muted-foreground mb-4 text-[13px]">
                <Link href={route('farms.animals.index', farm.id)} className="text-primary">
                    Животни
                </Link>{' '}
                / Ново животно
            </div>

            <h1 className="mb-6 text-[28px] font-bold tracking-tight">Добави животно</h1>

            <Panel className="max-w-2xl">
                <form onSubmit={submit} className="grid gap-5">
                    <FormField label="Ушна марка *" htmlFor="ear_tag" error={errors.ear_tag}>
                        <Input
                            id="ear_tag"
                            value={data.ear_tag}
                            onChange={(event) => setData('ear_tag', event.target.value)}
                            placeholder="BG0000000000"
                        />
                    </FormField>

                    <div className="grid gap-5 sm:grid-cols-2">
                        <FormField label="Вид *" htmlFor="species_id" error={errors.species_id}>
                            <SelectInput id="species_id" value={data.species_id} onChange={(event) => changeSpecies(event.target.value)}>
                                <option value="">– изберете –</option>
                                {species.map((item) => (
                                    <option key={item.id} value={item.id}>
                                        {item.name}
                                    </option>
                                ))}
                            </SelectInput>
                        </FormField>

                        <FormField label="Порода" htmlFor="breed_id" error={errors.breed_id}>
                            <SelectInput id="breed_id" value={data.breed_id} onChange={(event) => setData('breed_id', event.target.value)}>
                                <option value="">– без порода –</option>
                                {breeds.map((breed) => (
                                    <option key={breed.id} value={breed.id}>
                                        {breed.name}
                                    </option>
                                ))}
                            </SelectInput>
                        </FormField>
                    </div>

                    <div className="grid gap-5 sm:grid-cols-2">
                        <FormField label="Пол *" htmlFor="sex" error={errors.sex}>
                            <SelectInput id="sex" value={data.sex} onChange={(event) => setData('sex', event.target.value)}>
                                <option value="female">Женско</option>
                                <option value="male">Мъжко</option>
                            </SelectInput>
                        </FormField>

                        <FormField label="Дата на раждане" htmlFor="birth_date" error={errors.birth_date}>
                            <Input
                                id="birth_date"
                                type="date"
                                value={data.birth_date}
                                onChange={(event) => setData('birth_date', event.target.value)}
                            />
                        </FormField>
                    </div>

                    <div className="grid gap-5 sm:grid-cols-2">
                        <FormField label="Майка" htmlFor="mother_id" error={errors.mother_id}>
                            <SelectInput id="mother_id" value={data.mother_id} onChange={(event) => setData('mother_id', event.target.value)}>
                                <option value="">– не е известна –</option>
                                {mothersOfSpecies.map((mother) => (
                                    <option key={mother.id} value={mother.id}>
                                        {mother.ear_tag}
                                    </option>
                                ))}
                            </SelectInput>
                        </FormField>

                        <FormField label="Група" htmlFor="group_name" error={errors.group_name}>
                            <Input
                                id="group_name"
                                value={data.group_name}
                                onChange={(event) => setData('group_name', event.target.value)}
                                placeholder="напр. Дойно стадо"
                            />
                        </FormField>
                    </div>

                    <FormField label="Бележки" htmlFor="notes" error={errors.notes}>
                        <textarea
                            id="notes"
                            rows={3}
                            value={data.notes}
                            onChange={(event) => setData('notes', event.target.value)}
                            className="border-input focus-visible:ring-ring rounded-md border bg-white px-3 py-2 focus-visible:ring-2 focus-visible:outline-hidden"
                        />
                    </FormField>

                    <div className="flex gap-2.5">
                        <Button type="submit" disabled={processing}>
                            Запази
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={route('farms.animals.index', farm.id)}>Откажи</Link>
                        </Button>
                    </div>
                </form>
            </Panel>
        </AppLayout>
    );
}
