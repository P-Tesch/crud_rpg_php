<?php

test('the root url redirects to the sheet', function () {
    $this->get('/')->assertRedirect('/sheet');
});

test('guests are sent from the sheet to the login page', function () {
    $this->get('/sheet')->assertRedirect('/login');
});

test('the login page renders the inertia component', function () {
    $this->get('/login')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('vue/login'));
});
