// Малък цветен етикет, напр. за статус или вид на запис.
const colorClasses = {
    green: 'bg-green-100 text-green-800',
    amber: 'bg-amber-100 text-amber-800',
    red: 'bg-red-100 text-red-800',
    blue: 'bg-blue-100 text-blue-800',
    gray: 'bg-gray-100 text-gray-700',
};

export default function Pill({ color = 'gray', children }) {
    return <span className={`inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold ${colorClasses[color]}`}>{children}</span>;
}
