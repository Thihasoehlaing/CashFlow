<?php

it('redirects guests from the finance dashboard', function () {
    $this->get('/')->assertRedirect(route('login'));
});
