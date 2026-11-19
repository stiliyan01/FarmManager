import FormField from '@/components/form-field';
import { Input } from '@/components/ui/input';

// Полетата на препарата – едни и същи при създаване и при редакция.
export default function MedicationForm({ data, setData, errors }) {
    return (
        <>
            <FormField label="Име *" htmlFor="name" error={errors.name}>
                <Input id="name" value={data.name} onChange={(event) => setData('name', event.target.value)} />
            </FormField>

            <FormField label="Активно вещество" htmlFor="active_substance" error={errors.active_substance}>
                <Input id="active_substance" value={data.active_substance} onChange={(event) => setData('active_substance', event.target.value)} />
            </FormField>

            <div className="grid gap-5 sm:grid-cols-2">
                <FormField label="Карантина за мляко (дни) *" htmlFor="withdrawal_milk_days" error={errors.withdrawal_milk_days}>
                    <Input
                        id="withdrawal_milk_days"
                        type="number"
                        min="0"
                        value={data.withdrawal_milk_days}
                        onChange={(event) => setData('withdrawal_milk_days', event.target.value)}
                    />
                </FormField>

                <FormField label="Карантина за месо (дни) *" htmlFor="withdrawal_meat_days" error={errors.withdrawal_meat_days}>
                    <Input
                        id="withdrawal_meat_days"
                        type="number"
                        min="0"
                        value={data.withdrawal_meat_days}
                        onChange={(event) => setData('withdrawal_meat_days', event.target.value)}
                    />
                </FormField>
            </div>

            <p className="text-muted-foreground text-[13px]">
                0 дни – без карантина (напр. ваксини). Промяната важи за новите записи – старите пазят изчислените си дати.
            </p>
        </>
    );
}
