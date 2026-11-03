// Карта с едно число в горната част на таблото.
export default function StatCard({ label, value, note, valueColor = '' }) {
    return (
        <div className="bg-card rounded-lg border px-5 py-4">
            <div className="text-muted-foreground text-[13px]">{label}</div>
            <div className={`mt-1.5 text-[32px] font-bold tracking-tight ${valueColor}`}>{value}</div>
            <div className="text-muted-foreground mt-0.5 text-[13px]">{note}</div>
        </div>
    );
}
