function daysLeftText(daysLeft) {
    if (daysLeft === 0) {
        return 'изтича днес';
    }

    if (daysLeft === 1) {
        return 'още 1 ден';
    }

    return `още ${daysLeft} дни`;
}

// Кехлибарена лента в профила, когато животното е под карантинен срок.
export default function QuarantineBanner({ quarantine }) {
    return (
        <div
            role="status"
            className="mb-5 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3.5 text-amber-900"
        >
            <div className="font-bold">Под карантинен срок</div>

            {quarantine.milk && (
                <div>
                    Мляко: до <strong>{quarantine.milk.until}</strong> ({daysLeftText(quarantine.milk.days_left)})
                </div>
            )}

            {quarantine.meat && (
                <div>
                    Месо: до <strong>{quarantine.meat.until}</strong> ({daysLeftText(quarantine.meat.days_left)})
                </div>
            )}
        </div>
    );
}
