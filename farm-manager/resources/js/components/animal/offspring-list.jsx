import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';

// Разделът „Потомство“. Собственикът може да регистрира раждане от тук.
export default function OffspringList({ farm, animal, offspring, canEdit }) {
    const canRegisterBirth = canEdit && animal.is_active && animal.sex === 'female';

    return (
        <div className="mt-4">
            {canRegisterBirth && (
                <Button variant="outline" asChild className="mb-4">
                    <Link href={route('farms.animals.create', { farm: farm.id, mother_id: animal.id })}>+ Регистрирай раждане</Link>
                </Button>
            )}

            {offspring.length === 0 && <p className="text-muted-foreground">Няма регистрирано потомство.</p>}

            <ul className="flex flex-col gap-2">
                {offspring.map((child) => (
                    <li key={child.id} className="flex flex-wrap gap-x-3">
                        <Link href={route('animals.show', child.id)} className="text-primary font-semibold">
                            {child.ear_tag}
                        </Link>
                        <span className="text-gray-700">
                            {child.sex_label}
                            {child.birth_date && ` · родено на ${child.birth_date}`}
                        </span>
                    </li>
                ))}
            </ul>
        </div>
    );
}
