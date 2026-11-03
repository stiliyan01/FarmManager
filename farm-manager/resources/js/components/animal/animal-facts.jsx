import Panel from '@/components/panel';
import { Link } from '@inertiajs/react';

// Основните данни за животното в дясната колона на профила.
export default function AnimalFacts({ farm, animal, lastWeight }) {
    return (
        <Panel title="Данни">
            <dl className="grid grid-cols-2 gap-x-3 gap-y-2.5">
                <dt className="text-muted-foreground">Дата на раждане</dt>
                <dd className="font-medium">{animal.birth_date ?? '—'}</dd>

                <dt className="text-muted-foreground">Майка</dt>
                <dd className="font-medium">
                    {animal.mother ? (
                        <Link href={route('animals.show', animal.mother.id)} className="text-primary">
                            {animal.mother.ear_tag}
                        </Link>
                    ) : (
                        '—'
                    )}
                </dd>

                <dt className="text-muted-foreground">Последно тегло</dt>
                <dd className="font-medium">{lastWeight ? `${lastWeight.weight_kg} кг` : '—'}</dd>

                <dt className="text-muted-foreground">Група</dt>
                <dd className="font-medium">{animal.group_name ?? '—'}</dd>

                <dt className="text-muted-foreground">Ферма</dt>
                <dd className="font-medium">{farm.name}</dd>

                {!animal.is_active && (
                    <>
                        <dt className="text-muted-foreground">{animal.status_label} на</dt>
                        <dd className="font-medium">{animal.status_changed_at}</dd>
                    </>
                )}
            </dl>

            {animal.notes && <p className="mt-4 border-t pt-3 text-gray-700">{animal.notes}</p>}
        </Panel>
    );
}
