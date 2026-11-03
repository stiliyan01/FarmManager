// Обслужващият ветеринар на фермата.
export default function CurrentVetCard({ vet }) {
    if (!vet) {
        return (
            <section className="bg-card text-muted-foreground rounded-lg border px-5 py-4">
                Фермата все още няма назначен ветеринар. Назначава се от ОДБХ.
            </section>
        );
    }

    return (
        <section className="bg-card flex items-center gap-3.5 rounded-lg border px-5 py-4">
            <div className="bg-accent text-accent-foreground flex size-11 shrink-0 items-center justify-center rounded-full font-bold">
                {vet.name.replace('д-р ', '').charAt(0)}
            </div>
            <div>
                <div className="font-semibold">{vet.name}</div>
                <div className="text-muted-foreground mt-0.5 text-[13px]">
                    Обслужващ ветеринар от {vet.since}
                    {vet.phone && ` · тел. ${vet.phone}`}
                </div>
            </div>
        </section>
    );
}
