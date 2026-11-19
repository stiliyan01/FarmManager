import FormField from '@/components/form-field';
import Panel from '@/components/panel';
import Pill from '@/components/pill';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { invitationStatusColors } from '@/lib/colors';
import { useForm } from '@inertiajs/react';

// Собственикът на фермата или – докато го няма – поканите към него.
export default function OwnerPanel({ farm, owner, invitations }) {
    const { data, setData, post, processing, errors, reset } = useForm({ email: '' });

    function submit(event) {
        event.preventDefault();
        post(route('farms.invitations.store', farm.id), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    }

    if (owner) {
        return (
            <Panel title="Собственик">
                <div className="font-semibold">{owner.name}</div>
                <div className="text-muted-foreground mt-0.5 text-[13px] break-all">
                    {owner.email}
                    {owner.phone && ` · тел. ${owner.phone}`}
                </div>
            </Panel>
        );
    }

    return (
        <Panel title="Собственик">
            <p className="text-muted-foreground mb-4">
                Фермата още няма собственик. Той получава покана по имейл и става собственик, когато я приеме.
            </p>

            {invitations.length > 0 && (
                <ul className="mb-4 flex flex-col gap-2.5">
                    {invitations.map((invitation) => (
                        <li key={invitation.id} className="rounded-lg border px-3 py-2.5">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <span className="font-medium break-all">{invitation.email}</span>
                                <Pill color={invitationStatusColors[invitation.status]}>{invitation.status_label}</Pill>
                            </div>

                            {invitation.status === 'pending' && (
                                <>
                                    <div className="text-muted-foreground mt-1 text-[13px]">
                                        Валидна до {invitation.expires_at}. Линкът от писмото:
                                    </div>
                                    <Input
                                        readOnly
                                        value={invitation.url}
                                        onFocus={(event) => event.target.select()}
                                        aria-label="Линк към поканата"
                                        className="mt-1.5 h-9 text-xs md:text-xs"
                                    />
                                </>
                            )}
                        </li>
                    ))}
                </ul>
            )}

            <form onSubmit={submit} className="flex flex-wrap items-end gap-2.5">
                <div className="min-w-0 flex-1 basis-56">
                    <FormField label="Имейл на собственика" htmlFor="email" error={errors.email}>
                        <Input id="email" type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} />
                    </FormField>
                </div>
                <Button type="submit" disabled={processing}>
                    Изпрати покана
                </Button>
            </form>
        </Panel>
    );
}
