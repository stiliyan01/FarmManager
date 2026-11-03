import AppLogoIcon from '@/components/app-logo-icon';

// Страниците за вход, регистрация и забравена парола – формата е в средата на екрана.
export default function AuthSimpleLayout({ children, title, description }) {
    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="mb-8 flex flex-col items-center gap-4">
                    <div className="bg-menu-farm flex items-center gap-2.5 rounded-lg px-4 py-2.5 text-white">
                        <AppLogoIcon className="size-7 text-[#8FD1A8]" />
                        <span className="text-lg font-bold tracking-tight">Стадо</span>
                    </div>

                    <div className="space-y-2 text-center">
                        <h1 className="text-xl font-semibold">{title}</h1>
                        <p className="text-muted-foreground text-sm">{description}</p>
                    </div>
                </div>

                <div className="bg-card rounded-lg border p-6">{children}</div>
            </div>
        </div>
    );
}
