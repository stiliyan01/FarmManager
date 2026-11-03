import { Link } from '@inertiajs/react';

const settingsLinks = [
    { title: 'Профил', routeName: 'profile.edit' },
    { title: 'Парола', routeName: 'password.edit' },
];

// Страниците с настройки на акаунта – меню вляво и съдържание вдясно.
export default function SettingsLayout({ children }) {
    return (
        <div>
            <h1 className="mb-1 text-[28px] font-bold tracking-tight">Настройки</h1>
            <p className="text-muted-foreground mb-8">Вашият профил и парола</p>

            <div className="flex flex-col gap-8 lg:flex-row lg:gap-12">
                <nav className="flex gap-1 lg:w-48 lg:flex-col">
                    {settingsLinks.map((link) => (
                        <Link
                            key={link.routeName}
                            href={route(link.routeName)}
                            className={
                                route().current(link.routeName)
                                    ? 'bg-accent rounded-lg px-3 py-2.5 font-semibold'
                                    : 'rounded-lg px-3 py-2.5 hover:bg-white'
                            }
                        >
                            {link.title}
                        </Link>
                    ))}
                </nav>

                <div className="flex max-w-xl flex-1 flex-col gap-12">{children}</div>
            </div>
        </div>
    );
}
