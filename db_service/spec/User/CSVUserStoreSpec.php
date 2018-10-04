<?php

namespace spec\Datix\Server\User;

use Datix\Server\User\CSVUserStore;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CSVUserStoreSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CSVUserStore::class);
    }
}
