import AppLogo from '@/components/app-logo';
import { Link, usePage } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import { useState } from 'react';

// Цветът на менюто зависи от ролята: ферма (собственик, ветеринар), ОДБХ или администратор.
// theme сменя и основния цвят на бутоните и връзките (виж app.css).
const menuColors = {
    farm: { menu: 'bg-menu-farm', active: 'bg-menu-farm-active', user: 'bg-menu-farm-user', theme: '', section: null },
    odbh: { menu: 'bg-menu-odbh', active: 'bg-menu-odbh-active', user: 'bg-menu-odbh-user', theme: 'theme-odbh', section: 'ОДБХ' },
    admin: { menu: 'bg-menu-admin', active: 'bg-menu-admin-active', user: 'bg-menu-admin-user', theme: 'theme-admin', section: 'Админ' },
};

function getMenuColors(role) {
    if (role === 'registrar' || role === 'inspector') {
        return menuColors.odbh;
    }

    if (role === 'admin') {
        return menuColors.admin;
    }

    return menuColors.farm;
}

// Общото меню на ролята.
function getMainMenu(role, farm) {
    if (role === 'vet') {
        return [
            { title: 'Моят ден', href: route('vet.dashboard'), isActive: route().current('vet.dashboard') },
            { title: 'Препарати', href: route('medications.index'), isActive: route().current('medications.index') },
        ];
    }

    if (role === 'registrar') {
        return [
            {
                title: 'Ферми',
                href: route('farms.index'),
                isActive: route().current('farms.index') || route().current('farms.create') || route().current('farms.edit'),
            },
            { title: 'Покани', href: route('invitations.index'), isActive: route().current('invitations.index') },
        ];
    }

    if (role === 'inspector') {
        return [
            { title: 'Контрол', href: route('control'), isActive: route().current('control') },
            { title: 'Ферми в областта', href: route('farms.index'), isActive: route().current('farms.index') },
            { title: 'Проверки', href: route('inspections.index'), isActive: route().current('inspections.*') },
        ];
    }

    if (role === 'admin') {
        return [
            { title: 'Потребители', href: route('users.index'), isActive: route().current('users.*') },
            {
                title: 'Ферми',
                href: route('farms.index'),
                isActive: route().current('farms.index') || route().current('farms.create') || route().current('farms.edit'),
            },
            { title: 'Видове и породи', href: route('species.index'), isActive: route().current('species.*') },
            { title: 'Препарати', href: route('medications.index'), isActive: route().current('medications.*') },
            { title: 'Ваксинационни схеми', href: route('vaccination-schemes.index'), isActive: route().current('vaccination-schemes.*') },
            { title: 'Проверки', href: route('inspections.index'), isActive: route().current('inspections.*') },
            { title: 'Журнал', href: route('activity-log.index'), isActive: route().current('activity-log.*') },
        ];
    }

    // Собственикът работи в своята ферма и менюто му е менюто на фермата.
    // Извън нея (напр. в настройките) му трябва връзка обратно.
    if (!farm) {
        return [{ title: 'Моята ферма', href: route('dashboard'), isActive: false }];
    }

    return [];
}

// Страниците на фермата, която е отворена в момента.
function getFarmMenu(role, farm) {
    const items = [
        { title: 'Табло', href: route('farms.show', farm.id), isActive: route().current('farms.show') },
        {
            title: 'Животни',
            href: route('farms.animals.index', farm.id),
            isActive: route().current('farms.animals.*') || route().current('animals.*'),
        },
        {
            title: 'Ветеринарни записи',
            href: route('farms.vet-events.index', farm.id),
            isActive: route().current('farms.vet-events.index') || route().current('vet-events.*'),
        },
        { title: 'Отчети', href: route('farms.reports', farm.id), isActive: route().current('farms.reports') },
    ];

    if (role === 'vet') {
        items.push({
            title: '+ Нов запис',
            href: route('farms.vet-events.create', farm.id),
            isActive: route().current('farms.vet-events.create'),
        });
    }

    return items;
}

export default function AppLayout({ farm, children }) {
    const { auth, flash } = usePage().props;
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    const role = auth.user.role;
    const colors = getMenuColors(role);
    const mainItems = getMainMenu(role, farm);
    const farmItems = farm ? getFarmMenu(role, farm) : [];

    function linkClass(item) {
        if (item.isActive) {
            return `${colors.active} block rounded-lg px-3 py-2.5 font-semibold`;
        }

        return 'block rounded-lg px-3 py-2.5 text-white/75 hover:bg-white/5 hover:text-white';
    }

    return (
        <div className={`min-h-screen md:flex ${colors.theme}`}>
            {/* На телефон менюто е скрито зад бутон */}
            <div className={`${colors.menu} sticky top-0 z-20 flex items-center justify-between px-4 py-2 text-white md:hidden`}>
                <AppLogo section={colors.section} />
                <button
                    type="button"
                    onClick={() => setIsMenuOpen(!isMenuOpen)}
                    aria-label={isMenuOpen ? 'Затвори менюто' : 'Отвори менюто'}
                    aria-expanded={isMenuOpen}
                    className="flex h-11 w-11 items-center justify-center rounded-lg"
                >
                    {isMenuOpen ? <X /> : <Menu />}
                </button>
            </div>

            <nav
                aria-label="Основно меню"
                className={`${colors.menu} ${isMenuOpen ? 'flex' : 'hidden'} flex-col gap-6 px-4 pt-2 pb-6 text-white md:sticky md:top-0 md:flex md:h-screen md:w-60 md:shrink-0 md:overflow-y-auto md:pt-6`}
            >
                <div className="hidden px-2 md:block">
                    <AppLogo section={colors.section} />
                </div>

                {mainItems.length > 0 && (
                    <div className="flex flex-col gap-1">
                        {mainItems.map((item) => (
                            <Link key={item.title} href={item.href} className={linkClass(item)}>
                                {item.title}
                            </Link>
                        ))}
                    </div>
                )}

                {farm && (
                    <div className="flex flex-col gap-1">
                        <div className="truncate px-3 pb-1 text-xs font-semibold tracking-wide text-white/50 uppercase">{farm.name}</div>
                        {farmItems.map((item) => (
                            <Link key={item.title} href={item.href} className={linkClass(item)}>
                                {item.title}
                            </Link>
                        ))}
                    </div>
                )}

                <div className={`${colors.user} mt-auto rounded-lg p-3`}>
                    <div className="font-semibold">{auth.user.name}</div>
                    <div className="mt-0.5 text-[13px] text-white/60">
                        {auth.roleLabel}
                        {auth.regionName && ` · обл. ${auth.regionName}`}
                    </div>
                    <div className="mt-3 flex gap-4 text-[13px]">
                        <Link href={route('profile.edit')} className="text-white/75 hover:text-white">
                            Профил
                        </Link>
                        <Link href={route('logout')} method="post" as="button" className="text-white/75 hover:text-white">
                            Изход
                        </Link>
                    </div>
                </div>
            </nav>

            <main className="min-w-0 flex-1 px-4 py-6 md:px-8 md:py-8 xl:px-10">
                {flash.success && (
                    <div role="status" className="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                        {flash.success}
                    </div>
                )}

                {children}
            </main>
        </div>
    );
}
