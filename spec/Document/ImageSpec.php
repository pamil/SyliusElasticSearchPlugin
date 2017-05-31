<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use Sylius\ElasticSearchPlugin\Document\Image;
use PhpSpec\ObjectBehavior;

final class ImageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Image::class);
    }

    function it_has_code()
    {
        $this->setCode('abc');

        $this->getCode()->shouldReturn('abc');
    }

    function it_has_path()
    {
        $this->setPath('/abc');

        $this->getPath()->shouldReturn('/abc');
    }
}
