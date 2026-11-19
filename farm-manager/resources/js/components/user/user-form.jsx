import FormField from '@/components/form-field';
import SelectInput from '@/components/select-input';
import { Input } from '@/components/ui/input';

// Полетата на потребителя – едни и същи при създаване и при редакция.
export default function UserForm({ data, setData, errors, roles, regions, isNew }) {
    // Област има само служителят на ОДБХ – регистратор или инспектор.
    const needsRegion = data.role === 'registrar' || data.role === 'inspector';

    return (
        <>
            <div className="grid gap-5 sm:grid-cols-2">
                <FormField label="Име и фамилия *" htmlFor="name" error={errors.name}>
                    <Input id="name" value={data.name} onChange={(event) => setData('name', event.target.value)} />
                </FormField>

                <FormField label="Имейл *" htmlFor="email" error={errors.email}>
                    <Input id="email" type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} />
                </FormField>
            </div>

            <div className="grid gap-5 sm:grid-cols-2">
                <FormField label="Роля *" htmlFor="role" error={errors.role}>
                    <SelectInput id="role" value={data.role} onChange={(event) => setData('role', event.target.value)}>
                        {roles.map((role) => (
                            <option key={role.value} value={role.value}>
                                {role.label}
                            </option>
                        ))}
                    </SelectInput>
                </FormField>

                <FormField label="Телефон" htmlFor="phone" error={errors.phone}>
                    <Input id="phone" value={data.phone} onChange={(event) => setData('phone', event.target.value)} />
                </FormField>
            </div>

            {needsRegion && (
                <FormField label="Област (ОДБХ) *" htmlFor="region_id" error={errors.region_id}>
                    <SelectInput id="region_id" value={data.region_id} onChange={(event) => setData('region_id', event.target.value)}>
                        <option value="">– изберете –</option>
                        {regions.map((region) => (
                            <option key={region.id} value={region.id}>
                                {region.name}
                            </option>
                        ))}
                    </SelectInput>
                </FormField>
            )}

            <FormField label={isNew ? 'Парола *' : 'Нова парола'} htmlFor="password" error={errors.password}>
                <Input
                    id="password"
                    type="password"
                    autoComplete="new-password"
                    value={data.password}
                    onChange={(event) => setData('password', event.target.value)}
                />
                {!isNew && <p className="text-muted-foreground text-[13px]">Оставете празно, ако паролата не се сменя.</p>}
            </FormField>
        </>
    );
}
