import FormField from '@/components/form-field';
import SelectInput from '@/components/select-input';
import { Input } from '@/components/ui/input';

// Препарат, доза и карантинният срок на избрания препарат.
// Самите дати на карантината се смятат в сървъра при записа (CalculateWithdrawalDates).
export default function MedicationFields({ medications, data, setData, errors }) {
    const medication = medications.find((item) => item.id === Number(data.medication_id));

    let hasWithdrawal = false;
    if (medication) {
        hasWithdrawal = medication.withdrawal_milk_days > 0 || medication.withdrawal_meat_days > 0;
    }

    return (
        <>
            <div className="grid gap-5 sm:grid-cols-2">
                <FormField label="Препарат *" htmlFor="medication_id" error={errors.medication_id}>
                    <SelectInput id="medication_id" value={data.medication_id} onChange={(event) => setData('medication_id', event.target.value)}>
                        <option value="">– изберете –</option>
                        {medications.map((item) => (
                            <option key={item.id} value={item.id}>
                                {item.name}
                            </option>
                        ))}
                    </SelectInput>
                </FormField>

                <FormField label="Доза и приложение" htmlFor="dose" error={errors.dose}>
                    <Input id="dose" value={data.dose} onChange={(event) => setData('dose', event.target.value)} placeholder="напр. 10 ml, i/m" />
                </FormField>
            </div>

            {medication && hasWithdrawal && (
                <div className="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                    <div className="font-semibold">Карантинен срок на препарата</div>
                    <div className="mt-1 flex flex-wrap gap-x-6 gap-y-1">
                        <span>
                            Мляко: <strong>{medication.withdrawal_milk_days} дни</strong>
                        </span>
                        <span>
                            Месо: <strong>{medication.withdrawal_meat_days} дни</strong>
                        </span>
                    </div>
                    <div className="mt-1 text-[13px]">Датите се изчисляват автоматично при записа – от датата на мероприятието.</div>
                </div>
            )}

            {medication && !hasWithdrawal && (
                <div className="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-900">Препаратът няма карантинен срок.</div>
            )}
        </>
    );
}
