import Pill from '@/components/pill';
import { eventTypeColors } from '@/lib/colors';
import { Link } from '@inertiajs/react';

// Масовият запис се показва с броя животни, а единичният – с ушната марка.
function animalLabel(event) {
    if (event.animal_count > 1) {
        return <span className="font-semibold">{event.animal_count} животни</span>;
    }

    return (
        <Link href={route('animals.show', event.animal_id)} className="text-primary font-semibold">
            {event.ear_tag}
        </Link>
    );
}

// Последните ветеринарни записи на таблото: на телефон – списък, на широк екран – таблица.
export default function RecentEvents({ events }) {
    if (events.length === 0) {
        return <p className="text-muted-foreground">Все още няма ветеринарни записи.</p>;
    }

    return (
        <>
            <ul className="divide-y md:hidden">
                {events.map((event) => (
                    <li key={event.key} className="py-3 first:pt-0 last:pb-0">
                        <div className="flex items-center justify-between gap-3">
                            {animalLabel(event)}
                            <Pill color={eventTypeColors[event.type]}>{event.type_label}</Pill>
                        </div>
                        <div className="text-muted-foreground mt-1 text-[13px]">
                            {event.date}
                            {event.medication && ` · ${event.medication}`} · {event.vet}
                        </div>
                    </li>
                ))}
            </ul>

            <table className="hidden w-full md:table">
                <thead>
                    <tr className="text-muted-foreground border-b text-left text-xs">
                        <th className="px-2 py-2.5 font-medium">Дата</th>
                        <th className="px-2 py-2.5 font-medium">Животно</th>
                        <th className="px-2 py-2.5 font-medium">Вид</th>
                        <th className="px-2 py-2.5 font-medium">Препарат</th>
                        <th className="px-2 py-2.5 font-medium">Ветеринар</th>
                    </tr>
                </thead>
                <tbody>
                    {events.map((event) => (
                        <tr key={event.key} className="border-b last:border-0">
                            <td className="px-2 py-3 whitespace-nowrap text-gray-700">{event.date}</td>
                            <td className="px-2 py-3">{animalLabel(event)}</td>
                            <td className="px-2 py-3">
                                <Pill color={eventTypeColors[event.type]}>{event.type_label}</Pill>
                            </td>
                            <td className="px-2 py-3">{event.medication ?? '—'}</td>
                            <td className="px-2 py-3 text-gray-700">{event.vet}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </>
    );
}
