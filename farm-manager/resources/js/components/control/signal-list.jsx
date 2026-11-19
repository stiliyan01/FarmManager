import AnimalLinks from '@/components/animal-links';
import { Link } from '@inertiajs/react';

const signalColors = {
    red: { box: 'bg-red-50', title: 'text-red-800' },
    amber: { box: 'bg-amber-50', title: 'text-amber-800' },
};

// Сигналите за инспектора: просрочени ваксинации, закъснели записи, ферми без ветеринар.
export default function SignalList({ signals }) {
    if (signals.length === 0) {
        return <p className="text-muted-foreground">Няма сигнали – програмата се изпълнява навреме.</p>;
    }

    return (
        <div className="flex flex-col gap-2.5">
            {signals.map((signal) => (
                <div key={signal.key} className={`rounded-lg px-4 py-3 ${signalColors[signal.color].box}`}>
                    <div className={`font-semibold ${signalColors[signal.color].title}`}>{signal.title}</div>
                    <div className="mt-0.5 text-[13px] text-gray-700">
                        <Link href={route('farms.show', signal.farm_id)} className="font-medium underline underline-offset-2">
                            {signal.details}
                        </Link>
                    </div>
                    {signal.animals.length > 0 && <AnimalLinks animals={signal.animals} />}
                </div>
            ))}
        </div>
    );
}
