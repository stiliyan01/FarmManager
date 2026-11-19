// Цвят на етикета според вида на ветеринарния запис.
export const eventTypeColors = {
    vaccination: 'green',
    treatment: 'amber',
    exam: 'gray',
    test: 'blue',
};

// Цвят на точката в историята на животното.
export const eventDotColors = {
    vaccination: 'bg-green-700',
    treatment: 'bg-amber-600',
    exam: 'bg-gray-400',
    test: 'bg-blue-600',
};

export const animalStatusColors = {
    active: 'green',
    sold: 'gray',
    dead: 'gray',
};

// Статус на фермата в списъка на ОДБХ.
export const farmStatusColors = {
    active: 'green',
    without_vet: 'red',
    without_owner: 'amber',
};

export const invitationStatusColors = {
    pending: 'amber',
    accepted: 'green',
    expired: 'gray',
};

export const inspectionResultColors = {
    ok: 'green',
    violation: 'red',
};

// Оценката на ветеринаря в контролното табло.
export const ratingColors = {
    ok: 'green',
    watch: 'amber',
    risk: 'red',
};

export const roleColors = {
    owner: 'green',
    vet: 'green',
    registrar: 'blue',
    inspector: 'blue',
    admin: 'gray',
};
