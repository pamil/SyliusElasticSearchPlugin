<?php

declare(strict_types=1);

namespace spec\Sylius\ElasticSearchPlugin\Document;

use PhpSpec\ObjectBehavior;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;

final class ImageDocumentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ImageDocument::class);
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
