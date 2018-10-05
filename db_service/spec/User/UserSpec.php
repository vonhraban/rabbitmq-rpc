<?php

namespace spec\Datix\Server\User;

use Datix\User\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserSpec extends ObjectBehavior
{
    function it_can_build_itself_from_array()
    {
        $this->beConstructedThrough('fromArray', [
            [
            'first_name' => 'Lothaire',
            'last_name' => 'Spaxman',
            'email' => 'lspaxman6@marriott.com',
            'gender' => 'Male',
            'ip_address' => '159.167.87.195'
            ]
        ]);

        $this->toArray()->shouldReturn([
            'first_name' => 'Lothaire',
            'last_name' => 'Spaxman',
            'email' => 'lspaxman6@marriott.com',
            'gender' => 'Male',
            'ip_address' => '159.167.87.195'
        ]);
    }
}
