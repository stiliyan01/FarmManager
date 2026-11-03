import { cn } from '@/lib/utils';

// Обикновен <select> със същия вид като текстовите полета.
export default function SelectInput({ className = '', children, ...props }) {
    return (
        <select
            className={cn(
                'border-input focus-visible:ring-ring h-11 w-full rounded-md border bg-white px-3 text-sm focus-visible:ring-2 focus-visible:outline-hidden',
                className,
            )}
            {...props}
        >
            {children}
        </select>
    );
}
