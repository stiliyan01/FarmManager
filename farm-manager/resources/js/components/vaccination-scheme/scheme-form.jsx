import FormField from '@/components/form-field';
import SelectInput from '@/components/select-input';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

// Полетата на схемата – едни и същи при създаване и при редакция.
export default function SchemeForm({ data, setData, errors, species, medications }) {
    return (
        <>
            <div className="grid gap-5 sm:grid-cols-2">
                <FormField label="Вид *" htmlFor="species_id" error={errors.species_id}>
                    <SelectInput id="species_id" value={data.species_id} onChange={(event) => setData('species_id', event.target.value)}>
                        <option value="">– изберете –</option>
                        {species.map((item) => (
                            <option key={item.id} value={item.id}>
                                {item.name}
                            </option>
                        ))}
                    </SelectInput>
                </FormField>

                <FormField label="Име на схемата *" htmlFor="name" error={errors.name}>
                    <Input id="name" value={data.name} onChange={(event) => setData('name', event.target.value)} placeholder="напр. Антракс" />
                </FormField>
            </div>

            <div className="grid gap-5 sm:grid-cols-2">
                <FormField label="Ваксина *" htmlFor="medication_id" error={errors.medication_id}>
                    <SelectInput id="medication_id" value={data.medication_id} onChange={(event) => setData('medication_id', event.target.value)}>
                        <option value="">– изберете –</option>
                        {medications.map((medication) => (
                            <option key={medication.id} value={medication.id}>
                                {medication.name}
                            </option>
                        ))}
                    </SelectInput>
                </FormField>

                <FormField label="Интервал (дни) *" htmlFor="interval_days" error={errors.interval_days}>
                    <Input
                        id="interval_days"
                        type="number"
                        min="1"
                        value={data.interval_days}
                        onChange={(event) => setData('interval_days', event.target.value)}
                    />
                </FormField>
            </div>

            <div className="flex items-center gap-3">
                <Checkbox id="mandatory" checked={data.mandatory} onCheckedChange={(checked) => setData('mandatory', checked === true)} />
                <Label htmlFor="mandatory">Задължителна по държавната програма</Label>
            </div>
        </>
    );
}
