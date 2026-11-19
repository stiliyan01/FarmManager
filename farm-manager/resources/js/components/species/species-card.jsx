import InputError from '@/components/input-error';
import RenameForm from '@/components/species/rename-form';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/react';
import { useState } from 'react';

// Един вид с породите му: преименуване на вида и на породите, добавяне на нова порода.
export default function SpeciesCard({ species }) {
    const [isRenaming, setIsRenaming] = useState(false);
    const [renamingBreedId, setRenamingBreedId] = useState(null);
    const { data, setData, post, processing, errors, reset } = useForm({ name: '' });

    function addBreed(event) {
        event.preventDefault();
        post(route('breeds.store', species.id), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    return (
        <section className="bg-card rounded-lg border p-4 sm:p-5">
            {isRenaming && <RenameForm url={route('species.update', species.id)} value={species.name} onDone={() => setIsRenaming(false)} />}

            {!isRenaming && (
                <div className="flex items-center justify-between gap-3">
                    <div>
                        <h2 className="text-base font-semibold">{species.name}</h2>
                        <div className="text-muted-foreground text-[13px]">
                            {species.animals_count} животни · {species.breeds.length} породи
                        </div>
                    </div>
                    <button type="button" onClick={() => setIsRenaming(true)} className="text-primary h-11 text-[13px] font-medium">
                        Преименувай
                    </button>
                </div>
            )}

            <ul className="mt-3 divide-y border-y">
                {species.breeds.map((breed) => (
                    <li key={breed.id} className="py-1.5">
                        {renamingBreedId === breed.id && (
                            <RenameForm url={route('breeds.update', breed.id)} value={breed.name} onDone={() => setRenamingBreedId(null)} />
                        )}

                        {renamingBreedId !== breed.id && (
                            <div className="flex items-center justify-between gap-3">
                                <span>{breed.name}</span>
                                <button
                                    type="button"
                                    onClick={() => setRenamingBreedId(breed.id)}
                                    className="text-primary h-9 text-[13px] font-medium"
                                >
                                    Преименувай
                                </button>
                            </div>
                        )}
                    </li>
                ))}
            </ul>

            <form onSubmit={addBreed} className="mt-4 flex items-start gap-2">
                <div className="min-w-0 flex-1">
                    <Input
                        value={data.name}
                        onChange={(event) => setData('name', event.target.value)}
                        placeholder="Нова порода"
                        aria-label={`Нова порода за ${species.name}`}
                    />
                    <InputError message={errors.name} className="mt-1" />
                </div>
                <Button type="submit" variant="outline" disabled={processing}>
                    Добави
                </Button>
            </form>
        </section>
    );
}
