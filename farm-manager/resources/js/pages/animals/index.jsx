import Pill from '@/components/pill';
import SelectInput from '@/components/select-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { animalStatusColors } from '@/lib/colors';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function AnimalIndex({ farm, animals, canAddAnimal }) {
    const [search, setSearch] = useState('');
    const [status, setStatus] = useState('active');

    // Филтрираме направо в браузъра – животните в една ферма са най-много няколкостотин.
    const visibleAnimals = animals.filter((animal) => {
        if (status !== 'all' && animal.status !== status) {
            return false;
        }

        if (search !== '' && !animal.ear_tag.toLowerCase().includes(search.toLowerCase())) {
            return false;
        }

        return true;
    });

    return (
        <AppLayout farm={farm}>
            <Head title="Животни" />

            <header className="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <div className="text-muted-foreground text-[13px]">{farm.name}</div>
                    <h1 className="mt-1 text-[28px] font-bold tracking-tight">Животни</h1>
                </div>

                {canAddAnimal && (
                    <Button asChild>
                        <Link href={route('farms.animals.create', farm.id)}>+ Добави животно</Link>
                    </Button>
                )}
            </header>

            <div className="mb-4 flex flex-wrap gap-2.5">
                <Input
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                    placeholder="Търси по ушна марка"
                    aria-label="Търси по ушна марка"
                    className="max-w-xs"
                />
                <SelectInput value={status} onChange={(event) => setStatus(event.target.value)} aria-label="Статус" className="w-auto">
                    <option value="active">Активни</option>
                    <option value="sold">Продадени</option>
                    <option value="dead">Умрели</option>
                    <option value="all">Всички</option>
                </SelectInput>
            </div>

            <div className="bg-card overflow-x-auto rounded-lg border">
                <table className="w-full min-w-[720px]">
                    <thead>
                        <tr className="text-muted-foreground border-b text-left text-xs">
                            <th className="px-4 py-3 font-medium">Ушна марка</th>
                            <th className="px-4 py-3 font-medium">Вид / порода</th>
                            <th className="px-4 py-3 font-medium">Пол</th>
                            <th className="px-4 py-3 font-medium">Възраст</th>
                            <th className="px-4 py-3 font-medium">Група</th>
                            <th className="px-4 py-3 font-medium">Статус</th>
                            <th className="px-4 py-3 font-medium">Карантина</th>
                        </tr>
                    </thead>
                    <tbody>
                        {visibleAnimals.map((animal) => (
                            <tr key={animal.id} className="border-b last:border-0">
                                <td className="px-4 py-3">
                                    <Link href={route('animals.show', animal.id)} className="text-primary font-semibold">
                                        {animal.ear_tag}
                                    </Link>
                                </td>
                                <td className="px-4 py-3">
                                    {animal.species}
                                    {animal.breed && <span className="text-muted-foreground"> · {animal.breed}</span>}
                                </td>
                                <td className="px-4 py-3">{animal.sex_label}</td>
                                <td className="px-4 py-3">{animal.age}</td>
                                <td className="px-4 py-3 text-gray-700">{animal.group_name ?? '—'}</td>
                                <td className="px-4 py-3">
                                    <Pill color={animalStatusColors[animal.status]}>{animal.status_label}</Pill>
                                </td>
                                <td className="px-4 py-3">{animal.quarantine_until && <Pill color="amber">до {animal.quarantine_until}</Pill>}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                {visibleAnimals.length === 0 && <p className="text-muted-foreground px-4 py-6">Няма животни, които да отговарят на търсенето.</p>}
            </div>

            <p className="text-muted-foreground mt-3 text-[13px]">
                Показани: {visibleAnimals.length} от {animals.length}
            </p>
        </AppLayout>
    );
}
