// Карта с едно голямо число в горната част на таблата.
export default function StatCard({ label, value, note, valueColor = '' }) {
    return (
        <div className="bg-card rounded-lg border px-4 py-3.5 sm:px-5 sm:py-4">
            <div className="text-muted-foreground text-[13px]">{label}</div>
            <div className={`mt-1 text-[26px] font-bold tracking-tight sm:mt-1.5 sm:text-[32px] ${valueColor}`}>{value}</div>
            {note && <div className="text-muted-foreground mt-0.5 text-[13px]">{note}</div>}
        </div>
    );
}
