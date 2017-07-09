<?php

namespace seregazhuk\tests\Bot\Helpers;

use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Forms\Profile;

class FormTest extends TestCase
{
    /** @test */
    public function it_provides_only_those_fields_that_were_manually_set()
    {
        $form = new Profile();
        $form->setFirstName('name')
            ->setAbout('about');

        $formValues = ['name', 'about'];

        $this->assertCount(2, $form->toArray());
        $this->assertSame($formValues, array_values($form->toArray()));
    }
}