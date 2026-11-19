import Pill from '@/components/pill';
import { inspectionResultColors } from '@/lib/colors';

// Последната проверка от ОДБХ в долната част на таблото.
export default function LastInspectionCard({ inspection }) {
    if (!inspection) {
        return <section className="bg-card text-muted-foreground rounded-lg border px-5 py-4">Фермата още не е проверявана от ОДБХ.</section>;
    }

    return (
        <section className="bg-card flex items-center justify-between gap-3 rounded-lg border px-5 py-4">
            <div className="min-w-0">
                <div className="font-semibold">Последна проверка – {inspection.date}</div>
                <div className="text-muted-foreground mt-0.5 text-[13px]">{inspection.inspector}, ОДБХ</div>
            </div>
            <Pill color={inspectionResultColors[inspection.result]}>{inspection.result_label}</Pill>
        </section>
    );
}
