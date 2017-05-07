<?php

/*
 * This file is part of the `liip/LiipImagineBundle` project.
 *
 * (c) https://github.com/liip/LiipImagineBundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Liip\ImagineBundle\Tests\Functional\Binary;

use Liip\ImagineBundle\Binary\SimpleMimeTypeGuesser;
use Liip\ImagineBundle\Tests\Functional\AbstractWebTestCase;

class SimpleMimeTypeGuesserTest extends AbstractWebTestCase
{
    public function testCouldBeGetFromContainerAsService()
    {
        $this->createClient();

        $this->assertInstanceOf(
            SimpleMimeTypeGuesser::class,
            self::$kernel->getContainer()->get('liip_imagine.binary.mime_type_guesser')
        );
    }
}
