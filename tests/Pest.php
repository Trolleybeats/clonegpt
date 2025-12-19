<?php

pest()->extend(Tests\DuskTestCase::class)
    ->use(Illuminate\Foundation\Testing\DatabaseTruncation::class)
    //->use(Illuminate\Foundation\Testing\DatabaseMigrations::class)
    ->in('Browser');