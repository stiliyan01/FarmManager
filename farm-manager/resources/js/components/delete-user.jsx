import FormField from '@/components/form-field';
import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/react';

// Изтриване на собствения акаунт – с потвърждение с парола.
export default function DeleteUser() {
    const { data, setData, delete: destroy, processing, reset, errors, clearErrors } = useForm({ password: '' });

    function deleteUser(event) {
        event.preventDefault();
        destroy(route('profile.destroy'), {
            preserveScroll: true,
            onFinish: () => reset(),
        });
    }

    function closeDialog() {
        clearErrors();
        reset();
    }

    return (
        <div className="space-y-6">
            <HeadingSmall title="Изтриване на акаунта" description="Акаунтът и всичките му данни ще бъдат изтрити" />

            <div className="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 text-red-800">
                <p>Внимание: това действие не може да бъде отменено.</p>

                <Dialog>
                    <DialogTrigger asChild>
                        <Button variant="destructive">Изтрий акаунта</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogTitle>Сигурни ли сте, че искате да изтриете акаунта си?</DialogTitle>
                        <DialogDescription>Въведете паролата си, за да потвърдите.</DialogDescription>

                        <form onSubmit={deleteUser} className="grid gap-5">
                            <FormField label="Парола" htmlFor="delete_password" error={errors.password}>
                                <Input
                                    id="delete_password"
                                    type="password"
                                    autoComplete="current-password"
                                    value={data.password}
                                    onChange={(event) => setData('password', event.target.value)}
                                />
                            </FormField>

                            <DialogFooter>
                                <DialogClose asChild>
                                    <Button type="button" variant="secondary" onClick={closeDialog}>
                                        Откажи
                                    </Button>
                                </DialogClose>
                                <Button type="submit" variant="destructive" disabled={processing}>
                                    Изтрий акаунта
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    );
}
