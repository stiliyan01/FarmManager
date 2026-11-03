import Panel from '@/components/panel';

// Задължителните ваксинации и кога е следващата. Просрочените са в червено.
export default function UpcomingVaccinations({ vaccinations }) {
    return (
        <Panel title="Предстоящи ваксинации">
            {vaccinations.length === 0 && <p className="text-muted-foreground">Няма задължителни ваксинации за този вид.</p>}

            <div className="flex flex-col gap-2.5">
                {vaccinations.map((vaccination) => (
                    <div key={vaccination.scheme} className="flex justify-between gap-3">
                        <span>{vaccination.scheme}</span>

                        {vaccination.is_overdue && (
                            <span className="font-semibold text-red-800">
                                {vaccination.due_date ? `просрочено от ${vaccination.due_date}` : 'не е ваксинирано'}
                            </span>
                        )}

                        {!vaccination.is_overdue && <span className="text-gray-700">{vaccination.due_date}</span>}
                    </div>
                ))}
            </div>
        </Panel>
    );
}
