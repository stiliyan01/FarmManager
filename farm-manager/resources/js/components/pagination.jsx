import { Link } from '@inertiajs/react';

const buttonClass = 'border-input flex h-11 items-center rounded-md border bg-white px-4 text-sm font-medium';

// Страниране под списъците. Сървърът връща по 10 реда на страница (paginate(10) в контролера).
export default function Pagination({ paginator }) {
    if (paginator.total === 0) {
        return null;
    }

    return (
        <nav aria-label="Страници" className="mt-4 flex flex-wrap items-center justify-between gap-3">
            <p className="text-muted-foreground text-[13px]">
                Показани {paginator.from}–{paginator.to} от {paginator.total}
            </p>

            {paginator.last_page > 1 && (
                <div className="flex items-center gap-2">
                    {paginator.prev_page_url ? (
                        <Link href={paginator.prev_page_url} className={`${buttonClass} hover:bg-accent`}>
                            ← Назад
                        </Link>
                    ) : (
                        <span className={`${buttonClass} opacity-50`}>← Назад</span>
                    )}

                    <span className="px-1 text-[13px] whitespace-nowrap">
                        {paginator.current_page} / {paginator.last_page}
                    </span>

                    {paginator.next_page_url ? (
                        <Link href={paginator.next_page_url} className={`${buttonClass} hover:bg-accent`}>
                            Напред →
                        </Link>
                    ) : (
                        <span className={`${buttonClass} opacity-50`}>Напред →</span>
                    )}
                </div>
            )}
        </nav>
    );
}
