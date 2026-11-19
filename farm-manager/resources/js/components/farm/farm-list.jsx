import Pill from '@/components/pill';
import { farmStatusColors } from '@/lib/colors';
import { Link } from '@inertiajs/react';

// Списъкът с ферми за ОДБХ и админа: на телефон – карти, на широк екран – таблица.
export default function FarmList({ farms, canManage }) {
    if (farms.length === 0) {
        return <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Няма обекти, които да отговарят на търсенето.</p>;
    }

    // Регистраторът отива към управлението на обекта, инспекторът – към таблото му.
    function farmUrl(farm) {
        if (canManage) {
            return route('farms.edit', farm.id);
        }

        return route('farms.show', farm.id);
    }

    return (
        <>
            <div className="flex flex-col gap-2.5 lg:hidden">
                {farms.map((farm) => (
                    <Link key={farm.id} href={farmUrl(farm)} className="bg-card block rounded-lg border px-4 py-3">
                        <div className="flex items-start justify-between gap-3">
                            <div className="min-w-0">
                                <div className="text-primary font-semibold">{farm.name}</div>
                                <div className="text-muted-foreground text-[13px]">
                                    {farm.reg_number} · {farm.settlement}
                                </div>
                            </div>
                            <Pill color={farmStatusColors[farm.status]}>{farm.status_label}</Pill>
                        </div>

                        <div className="mt-2 grid gap-0.5 text-[13px] text-gray-700">
                            <div>Собственик: {farm.owner ?? 'няма'}</div>
                            <div>Ветеринар: {farm.vet ?? <span className="font-medium text-red-800">няма назначен</span>}</div>
                        </div>
                    </Link>
                ))}
            </div>

            <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                <table className="w-full">
                    <thead>
                        <tr className="text-muted-foreground border-b text-left text-xs">
                            <th className="px-4 py-3 font-medium">Рег. №</th>
                            <th className="px-4 py-3 font-medium">Обект</th>
                            <th className="px-4 py-3 font-medium">Собственик</th>
                            <th className="px-4 py-3 font-medium">Ветеринар</th>
                            <th className="px-4 py-3 font-medium">Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        {farms.map((farm) => (
                            <tr key={farm.id} className="border-b last:border-0">
                                <td className="px-4 py-3 whitespace-nowrap text-gray-700 tabular-nums">{farm.reg_number}</td>
                                <td className="px-4 py-3">
                                    <Link href={farmUrl(farm)} className="text-primary font-semibold">
                                        {farm.name}
                                    </Link>
                                    <div className="text-muted-foreground text-xs">{farm.settlement}</div>
                                </td>
                                <td className="px-4 py-3">{farm.owner ?? <span className="text-muted-foreground">няма</span>}</td>
                                <td className="px-4 py-3">{farm.vet ?? <span className="font-medium text-red-800">няма назначен</span>}</td>
                                <td className="px-4 py-3">
                                    <Pill color={farmStatusColors[farm.status]}>{farm.status_label}</Pill>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}
