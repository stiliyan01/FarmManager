import Pill from '@/components/pill';
import { eventTypeColors } from '@/lib/colors';
import { Link } from '@inertiajs/react';

// Препаратът с дозата, напр. „Пен-Стреп · 10 ml“.
function medicationText(event) {
    if (!event.medication) {
        return null;
    }

    if (event.dose) {
        return `${event.medication} · ${event.dose}`;
    }

    return event.medication;
}

// „мляко до 12.11 · месо до 30.11“ – само сроковете, които ги има.
function withdrawalText(event) {
    const parts = [];

    if (event.milk_until) {
        parts.push(`мляко до ${event.milk_until}`);
    }

    if (event.meat_until) {
        parts.push(`месо до ${event.meat_until}`);
    }

    return parts.join(' · ');
}

// Ветеринарните записи във фермата: на телефон – карти, на широк екран – таблица.
export default function VetEventList({ events }) {
    if (events.length === 0) {
        return <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Няма записи, които да отговарят на търсенето.</p>;
    }

    return (
        <>
            <div className="flex flex-col gap-2.5 lg:hidden">
                {events.map((event) => (
                    <div key={event.id} className="bg-card rounded-lg border px-4 py-3">
                        <div className="flex items-center justify-between gap-3">
                            <Link href={route('animals.show', event.animal_id)} className="text-primary font-semibold">
                                {event.ear_tag}
                            </Link>
                            <Pill color={eventTypeColors[event.type]}>{event.type_label}</Pill>
                        </div>

                        {medicationText(event) && <div className="mt-1.5 font-medium">{medicationText(event)}</div>}
                        {event.diagnosis && <div className="mt-0.5 text-gray-700">{event.diagnosis}</div>}
                        {withdrawalText(event) && <div className="mt-1.5 text-[13px] text-gray-600">Карантинен срок: {withdrawalText(event)}</div>}

                        <div className="text-muted-foreground mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px]">
                            <span>
                                {event.date} · {event.vet}
                            </span>
                            {event.entered_late_days && (
                                <span className="font-medium text-amber-800">въведен {event.entered_late_days} дни по-късно</span>
                            )}
                            {event.can_edit && (
                                <Link href={route('vet-events.edit', event.id)} className="text-primary font-medium">
                                    Редакция
                                </Link>
                            )}
                        </div>
                    </div>
                ))}
            </div>

            <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                <table className="w-full">
                    <thead>
                        <tr className="text-muted-foreground border-b text-left text-xs">
                            <th className="px-4 py-3 font-medium">Дата</th>
                            <th className="px-4 py-3 font-medium">Животно</th>
                            <th className="px-4 py-3 font-medium">Вид</th>
                            <th className="px-4 py-3 font-medium">Препарат и диагноза</th>
                            <th className="px-4 py-3 font-medium">Ветеринар</th>
                            <th className="px-4 py-3 font-medium">Карантина</th>
                            <th className="px-4 py-3 font-medium">
                                <span className="sr-only">Действия</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {events.map((event) => (
                            <tr key={event.id} className="border-b align-top last:border-0">
                                <td className="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {event.date}
                                    {event.entered_late_days && (
                                        <div className="text-xs font-medium text-amber-800">въведен след {event.entered_late_days} дни</div>
                                    )}
                                </td>
                                <td className="px-4 py-3">
                                    <Link href={route('animals.show', event.animal_id)} className="text-primary font-semibold">
                                        {event.ear_tag}
                                    </Link>
                                </td>
                                <td className="px-4 py-3">
                                    <Pill color={eventTypeColors[event.type]}>{event.type_label}</Pill>
                                </td>
                                <td className="px-4 py-3">
                                    {medicationText(event) && <div className="font-medium">{medicationText(event)}</div>}
                                    {event.diagnosis && <div className="text-gray-700">{event.diagnosis}</div>}
                                    {!medicationText(event) && !event.diagnosis && '—'}
                                </td>
                                <td className="px-4 py-3 text-gray-700">{event.vet}</td>
                                <td className="px-4 py-3 text-[13px] text-gray-600">{withdrawalText(event) || '—'}</td>
                                <td className="px-4 py-3 text-right">
                                    {event.can_edit && (
                                        <Link href={route('vet-events.edit', event.id)} className="text-primary font-medium">
                                            Редакция
                                        </Link>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}
