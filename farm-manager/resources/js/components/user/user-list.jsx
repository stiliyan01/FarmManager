import Pill from '@/components/pill';
import { roleColors } from '@/lib/colors';
import { Link } from '@inertiajs/react';

// Списъкът с потребители: на телефон – карти, на широк екран – таблица.
export default function UserList({ users }) {
    if (users.length === 0) {
        return <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Няма потребители, които да отговарят на търсенето.</p>;
    }

    return (
        <>
            <div className="flex flex-col gap-2.5 lg:hidden">
                {users.map((user) => (
                    <Link key={user.id} href={route('users.edit', user.id)} className="bg-card block rounded-lg border px-4 py-3">
                        <div className="flex items-start justify-between gap-3">
                            <div className="min-w-0">
                                <div className="text-primary font-semibold">{user.name}</div>
                                <div className="text-muted-foreground text-[13px] break-all">{user.email}</div>
                            </div>
                            <Pill color={roleColors[user.role]}>{user.role_label}</Pill>
                        </div>
                        <div className="mt-1.5 text-[13px] text-gray-700">
                            {user.scope} · последен вход: {user.last_login ?? 'никога'}
                        </div>
                    </Link>
                ))}
            </div>

            <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                <table className="w-full">
                    <thead>
                        <tr className="text-muted-foreground border-b text-left text-xs">
                            <th className="px-4 py-3 font-medium">Име</th>
                            <th className="px-4 py-3 font-medium">Роля</th>
                            <th className="px-4 py-3 font-medium">Обхват</th>
                            <th className="px-4 py-3 font-medium">Последен вход</th>
                            <th className="px-4 py-3 font-medium">
                                <span className="sr-only">Действия</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {users.map((user) => (
                            <tr key={user.id} className="border-b last:border-0">
                                <td className="px-4 py-3">
                                    <div className="font-semibold">{user.name}</div>
                                    <div className="text-muted-foreground text-xs">{user.email}</div>
                                </td>
                                <td className="px-4 py-3">
                                    <Pill color={roleColors[user.role]}>{user.role_label}</Pill>
                                </td>
                                <td className="px-4 py-3 text-gray-700">{user.scope}</td>
                                <td className="px-4 py-3 whitespace-nowrap text-gray-700">{user.last_login ?? '—'}</td>
                                <td className="px-4 py-3 text-right">
                                    <Link href={route('users.edit', user.id)} className="text-primary font-medium">
                                        Редакция
                                    </Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </>
    );
}
