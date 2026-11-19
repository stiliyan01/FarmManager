import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import Pill from '@/components/pill';
import AppLayout from '@/layouts/app-layout';
import { invitationStatusColors } from '@/lib/colors';
import { Head, Link } from '@inertiajs/react';

// Всички покани към собственици. Нова покана се изпраща от страницата на обекта.
export default function InvitationIndex({ invitations }) {
    return (
        <AppLayout>
            <Head title="Покани" />

            <PageHeader title="Покани към собственици" subtitle="Изпращат се от страницата на всеки обект" />

            {invitations.data.length === 0 && <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Още няма изпратени покани.</p>}

            {invitations.data.length > 0 && (
                <>
                    <div className="flex flex-col gap-2.5 lg:hidden">
                        {invitations.data.map((invitation) => (
                            <div key={invitation.id} className="bg-card rounded-lg border px-4 py-3">
                                <div className="flex items-start justify-between gap-3">
                                    <span className="min-w-0 font-semibold break-all">{invitation.email}</span>
                                    <Pill color={invitationStatusColors[invitation.status]}>{invitation.status_label}</Pill>
                                </div>
                                <Link href={route('farms.edit', invitation.farm_id)} className="text-primary mt-1 block font-medium">
                                    {invitation.farm}
                                </Link>
                                <div className="text-muted-foreground mt-1 text-[13px]">
                                    Изпратена {invitation.sent_at} · валидна до {invitation.expires_at}
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                        <table className="w-full">
                            <thead>
                                <tr className="text-muted-foreground border-b text-left text-xs">
                                    <th className="px-4 py-3 font-medium">Имейл</th>
                                    <th className="px-4 py-3 font-medium">Обект</th>
                                    <th className="px-4 py-3 font-medium">Изпратена</th>
                                    <th className="px-4 py-3 font-medium">Валидна до</th>
                                    <th className="px-4 py-3 font-medium">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                {invitations.data.map((invitation) => (
                                    <tr key={invitation.id} className="border-b last:border-0">
                                        <td className="px-4 py-3 font-medium">{invitation.email}</td>
                                        <td className="px-4 py-3">
                                            <Link href={route('farms.edit', invitation.farm_id)} className="text-primary font-semibold">
                                                {invitation.farm}
                                            </Link>
                                        </td>
                                        <td className="px-4 py-3 text-gray-700">{invitation.sent_at}</td>
                                        <td className="px-4 py-3 text-gray-700">{invitation.expires_at}</td>
                                        <td className="px-4 py-3">
                                            <Pill color={invitationStatusColors[invitation.status]}>{invitation.status_label}</Pill>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </>
            )}

            <Pagination paginator={invitations} />
        </AppLayout>
    );
}
