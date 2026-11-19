import Pill from '@/components/pill';
import { animalStatusColors } from '@/lib/colors';
import { Link } from '@inertiajs/react';

// Списъкът с животни: на телефон – карти, на широк екран – таблица.
export default function AnimalList({ animals }) {
    if (animals.length === 0) {
        return <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Няма животни, които да отговарят на търсенето.</p>;
    }

    return (
        <>
            <div className="flex flex-col gap-2.5 lg:hidden">
                {animals.map((animal) => (
                    <Link key={animal.id} href={route('animals.show', animal.id)} className="bg-card block rounded-lg border px-4 py-3">
                        <div className="flex items-center justify-between gap-3">
                            <span className="text-primary font-semibold">{animal.ear_tag}</span>
                            <Pill color={animalStatusColors[animal.status]}>{animal.status_label}</Pill>
                        </div>

                        <div className="mt-1 text-gray-700">
                            {animal.species}
                            {animal.breed && ` · ${animal.breed}`} · {animal.sex_label} · {animal.age}
                        </div>

                        {(animal.group_name || animal.quarantine_until) && (
                            <div className="mt-2 flex flex-wrap items-center gap-2 text-[13px] text-gray-600">
                                {animal.group_name && <span>{animal.group_name}</span>}
                                {animal.quarantine_until && <Pill color="amber">карантина до {animal.quarantine_until}</Pill>}
                            </div>
                        )}
                    </Link>
                ))}
            </div>

            <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                <table className="w-full">
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
                        {animals.map((animal) => (
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
                                <td className="px-4 py-3 whitespace-nowrap">{animal.age}</td>
                                <td className="px-4 py-3 text-gray-700">{animal.group_name ?? '—'}</td>
                                <td className="px-4 py-3">
                                    <Pill color={animalStatusColors[animal.status]}>{animal.status_label}</Pill>
                                </td>
                                <td className="px-4 py-3">{animal.quarantine_until && <Pill color="amber">до {animal.quarantine_until}</Pill>}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}
