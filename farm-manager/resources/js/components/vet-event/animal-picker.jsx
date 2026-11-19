import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { X } from 'lucide-react';
import { useState } from 'react';

// Избор на животни за записа – едно по едно или цяла група наведнъж (масова ваксинация).
export default function AnimalPicker({ animals, selectedIds, onChange }) {
    const [search, setSearch] = useState('');

    const selectedAnimals = animals.filter((animal) => selectedIds.includes(animal.id));

    const visibleAnimals = animals.filter((animal) => {
        const text = `${animal.ear_tag} ${animal.group_name ?? ''}`.toLowerCase();
        return text.includes(search.toLowerCase());
    });

    // Групите и колко животни има във всяка – за бутоните „цялата група“.
    const groups = {};
    for (const animal of animals) {
        if (animal.group_name) {
            groups[animal.group_name] = (groups[animal.group_name] ?? 0) + 1;
        }
    }

    function toggle(animalId) {
        if (selectedIds.includes(animalId)) {
            onChange(selectedIds.filter((id) => id !== animalId));
        } else {
            onChange([...selectedIds, animalId]);
        }
    }

    function selectGroup(groupName) {
        const ids = [...selectedIds];

        for (const animal of animals) {
            if (animal.group_name === groupName && !ids.includes(animal.id)) {
                ids.push(animal.id);
            }
        }

        onChange(ids);
    }

    return (
        <div className="grid gap-3">
            <div className="flex flex-wrap items-center gap-2">
                <span className="text-muted-foreground text-[13px]">Цяла група:</span>
                {Object.keys(groups).map((groupName) => (
                    <button
                        key={groupName}
                        type="button"
                        onClick={() => selectGroup(groupName)}
                        className="border-input hover:bg-accent h-9 rounded-full border bg-white px-3 text-[13px]"
                    >
                        {groupName} ({groups[groupName]})
                    </button>
                ))}
                <button
                    type="button"
                    onClick={() => onChange(animals.map((animal) => animal.id))}
                    className="border-input hover:bg-accent h-9 rounded-full border bg-white px-3 text-[13px]"
                >
                    Всички ({animals.length})
                </button>
            </div>

            {selectedAnimals.length > 0 && (
                <div className="bg-muted/50 rounded-lg p-2.5">
                    <div className="mb-2 flex items-center justify-between gap-3 text-[13px]">
                        <span className="font-semibold">Избрани: {selectedAnimals.length}</span>
                        <button type="button" onClick={() => onChange([])} className="text-primary font-medium">
                            Изчисти
                        </button>
                    </div>
                    <div className="flex max-h-28 flex-wrap gap-1.5 overflow-y-auto">
                        {selectedAnimals.map((animal) => (
                            <button
                                key={animal.id}
                                type="button"
                                onClick={() => toggle(animal.id)}
                                aria-label={`Махни ${animal.ear_tag}`}
                                className="bg-accent text-accent-foreground flex items-center gap-1 rounded-md px-2 py-1 text-[13px] font-medium"
                            >
                                {animal.ear_tag}
                                <X className="size-3.5" />
                            </button>
                        ))}
                    </div>
                </div>
            )}

            <Input
                type="search"
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="Търси по ушна марка или група…"
                aria-label="Търси животно"
            />

            {/* relative – скритите полета на отметките да остават в кутията и да не удължават страницата */}
            <ul className="relative max-h-72 divide-y overflow-y-auto rounded-lg border bg-white">
                {visibleAnimals.length === 0 && <li className="text-muted-foreground px-3 py-3">Няма такова животно.</li>}

                {visibleAnimals.map((animal) => (
                    <li key={animal.id}>
                        <label className="flex min-h-11 cursor-pointer items-center gap-3 px-3 py-2 hover:bg-gray-50">
                            <Checkbox checked={selectedIds.includes(animal.id)} onCheckedChange={() => toggle(animal.id)} />
                            <span className="font-medium">{animal.ear_tag}</span>
                            <span className="text-muted-foreground text-[13px]">
                                {animal.species}
                                {animal.group_name && ` · ${animal.group_name}`}
                            </span>
                        </label>
                    </li>
                ))}
            </ul>
        </div>
    );
}
