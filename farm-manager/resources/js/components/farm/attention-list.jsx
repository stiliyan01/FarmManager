import AnimalLinks from '@/components/farm/animal-links';

// „Изисква внимание“: просрочени ваксинации (червено) и животни под карантина (кехлибарено).
export default function AttentionList({ overdueAnimals, quarantinedAnimals }) {
    if (overdueAnimals.length === 0 && quarantinedAnimals.length === 0) {
        return <p className="text-muted-foreground">Няма нищо, което да изисква внимание.</p>;
    }

    return (
        <div className="flex flex-col gap-2.5">
            {overdueAnimals.length > 0 && (
                <div className="rounded-lg bg-red-50 px-4 py-3">
                    <div className="font-semibold text-red-800">Просрочена ваксинация – {overdueAnimals.length} животни</div>
                    <AnimalLinks animals={overdueAnimals} />
                </div>
            )}

            {quarantinedAnimals.length > 0 && (
                <div className="rounded-lg bg-amber-50 px-4 py-3">
                    <div className="font-semibold text-amber-800">Под карантинен срок – {quarantinedAnimals.length} животни</div>
                    <div className="mt-1 text-[13px] text-gray-700">Първият срок изтича на {quarantinedAnimals[0].until}</div>
                    <AnimalLinks animals={quarantinedAnimals} />
                </div>
            )}
        </div>
    );
}
