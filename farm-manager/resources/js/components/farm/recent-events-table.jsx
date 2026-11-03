import Pill from '@/components/pill';
import { eventTypeColors } from '@/lib/colors';
import { Link } from '@inertiajs/react';

export default function RecentEventsTable({ events }) {
    if (events.length === 0) {
        return <p className="text-muted-foreground">Все още няма ветеринарни записи.</p>;
    }

    return (
        <div className="overflow-x-auto">
            <table className="w-full min-w-[560px]">
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
                            <td className="px-2 py-3 text-gray-700">{event.date}</td>
                            <td className="px-2 py-3">
                                {/* Масов запис – показваме броя животни вместо една ушна марка */}
                                {event.animal_count > 1 ? (
                                    <span className="font-semibold">{event.animal_count} животни</span>
                                ) : (
                                    <Link href={route('animals.show', event.animal_id)} className="text-primary font-semibold">
                                        {event.ear_tag}
                                    </Link>
                                )}
                            </td>
                            <td className="px-2 py-3">
                                <Pill color={eventTypeColors[event.type]}>{event.type_label}</Pill>
                            </td>
                            <td className="px-2 py-3">{event.medication ?? '—'}</td>
                            <td className="px-2 py-3 text-gray-700">{event.vet}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
