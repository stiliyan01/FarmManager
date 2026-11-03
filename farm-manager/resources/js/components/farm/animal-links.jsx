import { Link } from '@inertiajs/react';

// Първите няколко ушни марки като връзки към профила, а за останалите – „и още N“.
export default function AnimalLinks({ animals, limit = 5 }) {
    const shownAnimals = animals.slice(0, limit);
    const hiddenCount = animals.length - shownAnimals.length;

    return (
        <div className="mt-1 text-[13px] text-gray-700">
            {shownAnimals.map((animal, index) => (
                <span key={animal.id}>
                    {index > 0 && ', '}
                    <Link href={route('animals.show', animal.id)} className="font-medium underline underline-offset-2">
                        {animal.ear_tag}
                    </Link>
                </span>
            ))}
            {hiddenCount > 0 && ` и още ${hiddenCount}`}
        </div>
    );
}
