import AppLogo from '@/components/app-logo';
import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { useState } from 'react';

// Цветът на менюто зависи от ролята: ферма (собственик, ветеринар), ОДБХ или администратор.
const menuColors = {
    farm: { menu: 'bg-menu-farm', active: 'bg-menu-farm-active', user: 'bg-menu-farm-user' },
    odbh: { menu: 'bg-menu-odbh', active: 'bg-menu-odbh-active', user: 'bg-menu-odbh-user' },
    admin: { menu: 'bg-menu-admin', active: 'bg-menu-admin-active', user: 'bg-menu-admin-user' },
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

// Връзките в менюто. Когато сме в някоя ферма, се показват и нейните страници.
function getMenuItems(farm) {
    if (!farm) {
        return [{ title: 'Табло', href: route('dashboard'), isActive: route().current('dashboard') }];
    }

    return [
        {
            title: 'Табло',
            href: route('farms.show', farm.id),
            isActive: route().current('farms.show'),
        },
        {
            title: 'Животни',
            href: route('farms.animals.index', farm.id),
            isActive: route().current('farms.animals.*') || route().current('animals.*'),
        },
    ];
}

export default function AppLayout({ farm, children }) {
    const { auth, flash } = usePage().props;
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    const colors = getMenuColors(auth.user.role);
    const menuItems = getMenuItems(farm);

    return (
        <div className="min-h-screen md:flex">
            {/* На телефон менюто е скрито зад бутон */}
            <div className={`${colors.menu} flex items-center justify-between px-4 py-2 text-white md:hidden`}>
                <AppLogo />
                <button
                    type="button"
                    onClick={() => setIsMenuOpen(!isMenuOpen)}
                    aria-label="Меню"
                    className="flex h-11 w-11 items-center justify-center rounded-lg"
                >
                    <Menu />
                </button>
            </div>

            <nav
                aria-label="Основно меню"
                className={`${colors.menu} ${isMenuOpen ? 'flex' : 'hidden'} flex-col gap-7 px-4 py-6 text-white md:flex md:w-60 md:shrink-0`}
            >
                <div className="hidden px-2 md:block">
                    <AppLogo />
                </div>

                <div className="flex flex-col gap-1">
                    {menuItems.map((item) => (
                        <Link
                            key={item.title}
                            href={item.href}
                            className={
                                item.isActive
                                    ? `${colors.active} rounded-lg px-3 py-3 font-semibold`
                                    : 'rounded-lg px-3 py-3 text-white/75 hover:text-white'
                            }
                        >
                            {item.title}
                        </Link>
                    ))}
                </div>

                <div className={`${colors.user} mt-auto rounded-lg p-3`}>
                    <div className="font-semibold">{auth.user.name}</div>
                    <div className="mt-0.5 text-[13px] text-white/60">{auth.roleLabel}</div>
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

            <main className="min-w-0 flex-1 px-4 py-6 md:px-10 md:py-8">
                {flash.success && <div className="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{flash.success}</div>}

                {children}
            </main>
        </div>
    );
}
