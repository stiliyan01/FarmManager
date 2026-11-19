<?php

use App\Exports\ReportExport;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\Region;
use App\Models\User;
use App\Models\VetEvent;
use Maatwebsite\Excel\Facades\Excel;

test('owner downloads the vet records for a period as Excel and PDF', function () {
    Excel::fake();

    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id, 'reg_number' => '9700-0011']);
    $animal = Animal::factory()->create(['farm_id' => $farm->id]);
    VetEvent::factory()->create(['animal_id' => $animal->id, 'event_date' => today()->subDays(3)]);
    VetEvent::factory()->create(['animal_id' => $animal->id, 'event_date' => today()->subYears(2)]);

    $period = ['from' => today()->subMonth()->toDateString(), 'to' => today()->toDateString()];

    $this->actingAs($owner)->get(route('farms.reports.vet-events', ['farm' => $farm, 'format' => 'excel', ...$period]));

    // Само записът от избрания период.
    Excel::assertDownloaded('veterinarni-zapisi-9700-0011.xlsx', function (ReportExport $export) {
        return count($export->array()) === 1;
    });

    $this->actingAs($owner)
        ->get(route('farms.reports.vet-events', ['farm' => $farm, 'format' => 'pdf', ...$period]))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('the end of the period cannot be before the start', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);

    $this->actingAs($owner)
        ->get(route('farms.reports.vet-events', [
            'farm' => $farm,
            'format' => 'excel',
            'from' => today()->toDateString(),
            'to' => today()->subMonth()->toDateString(),
        ]))
        ->assertSessionHasErrors('to');
});

test('another owner cannot download the reports of the farm', function () {
    $farm = Farm::factory()->create();
    $otherOwner = User::factory()->create();

    $this->actingAs($otherOwner)
        ->get(route('farms.reports.animals', ['farm' => $farm, 'format' => 'excel']))
        ->assertForbidden();
});

test('inspector downloads the report for own region', function () {
    Excel::fake();

    $region = Region::factory()->create();
    $inspector = User::factory()->inspector($region)->create();
    Farm::factory()->count(2)->create(['region_id' => $region->id]);
    Farm::factory()->create();

    $this->actingAs($inspector)->get(route('reports.region', 'excel'));

    Excel::assertDownloaded('otchet-oblast-'.today()->format('Y-m-d').'.xlsx', function (ReportExport $export) {
        return count($export->array()) === 2;
    });
});
