import { cn } from '@/lib/utils';

// Бяла карта с рамка – основният блок на страниците.
export default function Panel({ title, className = '', children }) {
    return (
        <section className={cn('bg-card rounded-lg border p-5', className)}>
            {title && <h2 className="mb-3 text-base font-semibold">{title}</h2>}
            {children}
        </section>
    );
}
