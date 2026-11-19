import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useState } from 'react';

// Поле за търсене. Търси при Enter или при натискане на бутона.
export default function SearchForm({ value, placeholder, onSearch }) {
    const [text, setText] = useState(value ?? '');

    function submit(event) {
        event.preventDefault();
        onSearch(text);
    }

    return (
        <form onSubmit={submit} className="flex w-full gap-2 sm:w-auto">
            <Input
                type="search"
                value={text}
                onChange={(event) => setText(event.target.value)}
                placeholder={placeholder}
                aria-label={placeholder}
                className="sm:w-72"
            />
            <Button type="submit" variant="outline">
                Търси
            </Button>
        </form>
    );
}
