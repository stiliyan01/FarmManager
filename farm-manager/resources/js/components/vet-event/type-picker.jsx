import InputError from '@/components/input-error';

// Видът на записа като група бутони: Ваксинация, Лечение, Преглед, Изследване.
export default function TypePicker({ types, value, onChange, error }) {
    return (
        <fieldset>
            <legend className="mb-2 text-sm font-medium">Вид на записа</legend>

            <div className="bg-muted grid grid-cols-2 gap-1 rounded-lg p-1 sm:inline-grid sm:grid-cols-4">
                {types.map((type) => (
                    <button
                        key={type.value}
                        type="button"
                        aria-pressed={value === type.value}
                        onClick={() => onChange(type.value)}
                        className={
                            value === type.value
                                ? 'h-10 rounded-md bg-white px-4 font-semibold shadow-sm'
                                : 'text-muted-foreground h-10 rounded-md px-4 hover:text-gray-900'
                        }
                    >
                        {type.label}
                    </button>
                ))}
            </div>

            <InputError message={error} className="mt-2" />
        </fieldset>
    );
}
