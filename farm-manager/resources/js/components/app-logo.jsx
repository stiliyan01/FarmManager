import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    return (
        <div className="flex items-center gap-2.5">
            <AppLogoIcon className="size-7 text-[#8FD1A8]" />
            <span className="text-lg font-bold tracking-tight">Стадо</span>
        </div>
    );
}
