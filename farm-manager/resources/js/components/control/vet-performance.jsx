import Pill from '@/components/pill';
import { ratingColors } from '@/lib/colors';

// Лентата „Изпълнение на програмата“ – зелена, кехлибарена или червена според оценката.
const barColors = {
    ok: 'bg-green-700',
    watch: 'bg-amber-600',
    risk: 'bg-red-700',
};

function percentText(percent) {
    if (percent === null) {
        return '—';
    }

    return `${percent}%`;
}

// „Изпълнение по ветеринар“ на контролното табло: на телефон – карти, на широк екран – таблица.
export default function VetPerformance({ vets }) {
    if (vets.length === 0) {
        return <p className="text-muted-foreground">В областта още няма назначени ветеринари.</p>;
    }

    return (
        <>
            <ul className="divide-y lg:hidden">
                {vets.map((vet) => (
                    <li key={vet.id} className="py-3 first:pt-0 last:pb-0">
                        <div className="flex items-center justify-between gap-3">
                            <span className="font-semibold">{vet.name}</span>
                            <Pill color={ratingColors[vet.rating]}>{vet.rating_label}</Pill>
                        </div>
                        <div className="mt-2 flex items-center gap-3">
                            <div className="h-2 flex-1 overflow-hidden rounded bg-gray-200">
                                <div className={`h-2 rounded ${barColors[vet.rating]}`} style={{ width: `${vet.percent ?? 0}%` }} />
                            </div>
                            <span className="w-10 text-right font-semibold">{percentText(vet.percent)}</span>
                        </div>
                        <div className="text-muted-foreground mt-1.5 text-[13px]">
                            {vet.farms} ферми · {vet.animals} животни · просрочени: {vet.overdue} · закъснели записи: {vet.late}
                        </div>
                    </li>
                ))}
            </ul>

            <table className="hidden w-full lg:table">
                <thead>
                    <tr className="text-muted-foreground border-b text-left text-xs">
                        <th className="px-2 py-2.5 font-medium">Ветеринар</th>
                        <th className="px-2 py-2.5 font-medium">Ферми</th>
                        <th className="px-2 py-2.5 font-medium">Животни</th>
                        <th className="w-56 px-2 py-2.5 font-medium">Изпълнение на програмата</th>
                        <th className="px-2 py-2.5 font-medium">Просрочени</th>
                        <th className="px-2 py-2.5 font-medium">Закъснели записи</th>
                        <th className="px-2 py-2.5 font-medium">Оценка</th>
                    </tr>
                </thead>
                <tbody>
                    {vets.map((vet) => (
                        <tr key={vet.id} className="border-b last:border-0">
                            <td className="px-2 py-3 font-semibold">{vet.name}</td>
                            <td className="px-2 py-3">{vet.farms}</td>
                            <td className="px-2 py-3">{vet.animals}</td>
                            <td className="px-2 py-3">
                                <div className="flex items-center gap-2.5">
                                    <div className="h-2 flex-1 overflow-hidden rounded bg-gray-200">
                                        <div className={`h-2 rounded ${barColors[vet.rating]}`} style={{ width: `${vet.percent ?? 0}%` }} />
                                    </div>
                                    <span className="w-10 text-right font-semibold">{percentText(vet.percent)}</span>
                                </div>
                            </td>
                            <td className={vet.overdue > 0 ? 'px-2 py-3 font-semibold text-red-800' : 'px-2 py-3'}>{vet.overdue}</td>
                            <td className="px-2 py-3">{vet.late}</td>
                            <td className="px-2 py-3">
                                <Pill color={ratingColors[vet.rating]}>{vet.rating_label}</Pill>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </>
    );
}
