// Заглавието на страницата: ред с пояснение, голямо заглавие и бутоните вдясно.
// На телефон бутоните падат под заглавието.
export default function PageHeader({ title, subtitle, children }) {
    return (
        <header className="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div className="min-w-0">
                {subtitle && <div className="text-muted-foreground text-[13px]">{subtitle}</div>}
                <h1 className="mt-1 text-2xl font-bold tracking-tight md:text-[28px]">{title}</h1>
            </div>

            {children && <div className="flex flex-wrap gap-2.5">{children}</div>}
        </header>
    );
}
