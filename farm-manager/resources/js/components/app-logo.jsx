import AppLogoIcon from '@/components/app-logo-icon';

// Логото в менюто. За ОДБХ и админа след името стои и разделът – „Стадо · ОДБХ“.
export default function AppLogo({ section }) {
    return (
        <div className="flex items-center gap-2.5">
            <AppLogoIcon className="size-7 text-[#8FD1A8]" />
            <span className="text-lg font-bold tracking-tight">
                Стадо
                {section && <span className="font-semibold text-white/70"> · {section}</span>}
            </span>
        </div>
    );
}
