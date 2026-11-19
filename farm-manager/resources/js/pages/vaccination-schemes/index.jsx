import PageHeader from '@/components/page-header';
import Pagination from '@/components/pagination';
import Pill from '@/components/pill';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';

// Ваксинационните схеми – по задължителните се смятат предстоящите и просрочените ваксинации.
export default function VaccinationSchemeIndex({ schemes }) {
    return (
        <AppLayout>
            <Head title="Ваксинационни схеми" />

            <PageHeader title="Ваксинационни схеми" subtitle="Държавна профилактична програма по вид животно">
                <Button asChild>
                    <Link href={route('vaccination-schemes.create')}>+ Нова схема</Link>
                </Button>
            </PageHeader>

            {schemes.data.length === 0 && <p className="bg-card text-muted-foreground rounded-lg border px-4 py-6">Още няма схеми.</p>}

            {schemes.data.length > 0 && (
                <>
                    <div className="flex flex-col gap-2.5 lg:hidden">
                        {schemes.data.map((scheme) => (
                            <Link
                                key={scheme.id}
                                href={route('vaccination-schemes.edit', scheme.id)}
                                className="bg-card block rounded-lg border px-4 py-3"
                            >
                                <div className="flex items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <div className="text-primary font-semibold">
                                            {scheme.species} – {scheme.name}
                                        </div>
                                        <div className="text-muted-foreground text-[13px]">{scheme.medication}</div>
                                    </div>
                                    {scheme.mandatory ? <Pill color="green">задължителна</Pill> : <Pill>по желание</Pill>}
                                </div>
                                <div className="mt-1.5 text-[13px] text-gray-700">Всеки {scheme.interval_days} дни</div>
                            </Link>
                        ))}
                    </div>

                    <div className="bg-card hidden overflow-hidden rounded-lg border lg:block">
                        <table className="w-full">
                            <thead>
                                <tr className="text-muted-foreground border-b text-left text-xs">
                                    <th className="px-4 py-3 font-medium">Вид</th>
                                    <th className="px-4 py-3 font-medium">Схема</th>
                                    <th className="px-4 py-3 font-medium">Ваксина</th>
                                    <th className="px-4 py-3 font-medium">Интервал</th>
                                    <th className="px-4 py-3 font-medium">Задължителна</th>
                                    <th className="px-4 py-3 font-medium">
                                        <span className="sr-only">Действия</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {schemes.data.map((scheme) => (
                                    <tr key={scheme.id} className="border-b last:border-0">
                                        <td className="px-4 py-3">{scheme.species}</td>
                                        <td className="px-4 py-3 font-semibold">{scheme.name}</td>
                                        <td className="px-4 py-3 text-gray-700">{scheme.medication}</td>
                                        <td className="px-4 py-3">{scheme.interval_days} дни</td>
                                        <td className="px-4 py-3">{scheme.mandatory ? <Pill color="green">да</Pill> : <Pill>не</Pill>}</td>
                                        <td className="px-4 py-3 text-right">
                                            <Link href={route('vaccination-schemes.edit', scheme.id)} className="text-primary font-medium">
                                                Редакция
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </>
            )}

            <Pagination paginator={schemes} />
        </AppLayout>
    );
}
