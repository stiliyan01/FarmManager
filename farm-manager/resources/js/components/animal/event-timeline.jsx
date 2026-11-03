import Pill from '@/components/pill';
import { eventDotColors, eventTypeColors } from '@/lib/colors';

// Заглавие на записа: препаратът (и дозата), а ако няма препарат – видът на записа.
function eventTitle(event) {
    if (!event.medication) {
        return event.type_label;
    }

    if (event.dose) {
        return `${event.medication} · ${event.dose}`;
    }

    return event.medication;
}

// Ветеринарната история като времева линия – най-новите записи са най-горе.
export default function EventTimeline({ events }) {
    if (events.length === 0) {
        return <p className="text-muted-foreground mt-4">Няма записи.</p>;
    }

    return (
        <ol className="mt-4">
            {events.map((event) => (
                <li key={event.id} className="flex gap-4">
                    <div className="flex shrink-0 flex-col items-center">
                        <div className={`mt-1 size-3 rounded-full ${eventDotColors[event.type]}`} />
                        <div className="bg-border min-h-5 w-0.5 flex-1" />
                    </div>

                    <div className="min-w-0 pb-5">
                        <div className="flex flex-wrap items-center gap-2.5">
                            <span className="font-semibold">{eventTitle(event)}</span>
                            <Pill color={eventTypeColors[event.type]}>{event.type_label}</Pill>
                        </div>
                        {event.diagnosis && <div className="mt-1 text-gray-700">{event.diagnosis}</div>}
                        {event.notes && <div className="mt-1 text-gray-700">{event.notes}</div>}
                        <div className="text-muted-foreground mt-1 text-xs">
                            {event.date} · {event.vet}
                        </div>
                    </div>
                </li>
            ))}
        </ol>
    );
}
