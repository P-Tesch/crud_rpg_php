<?php

use App\Filament\AdminLogin;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

function makeAdmin(): User
{
    return User::forceCreate([
        'login' => 'admin',
        'password' => 'password',
        'is_admin' => true,
    ]);
}

test('the admin login page renders', function () {
    $this->get('/admin/login')->assertOk();
});

test('an admin can log in through the custom login field', function () {
    makeAdmin();

    livewire(AdminLogin::class)
        ->fillForm(['login' => 'admin', 'password' => 'password'])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    $this->assertAuthenticated();
});

test('every resource index page loads for an admin', function () {
    $this->actingAs(makeAdmin());

    $resources = Filament::getPanel('admin')?->getResources() ?? [];
    expect($resources)->toHaveCount(15);

    foreach ($resources as $resource) {
        $this->get($resource::getUrl('index'))->assertOk();
    }
});

test('edit and view pages render with seeded data', function () {
    $this->seed();
    $this->actingAs(User::where('is_admin', true)->firstOrFail());

    foreach (Filament::getPanel('admin')?->getResources() ?? [] as $resource) {
        $record = $resource::getModel()::query()->first();

        if ($record === null) {
            continue;
        }

        foreach (['edit', 'view'] as $page) {
            if ($resource::hasPage($page)) {
                $this->get($resource::getUrl($page, ['record' => $record]))->assertOk();
            }
        }
    }
});
