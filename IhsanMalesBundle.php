<?php

namespace Ihsan\MalesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IhsanMalesBundle extends Bundle
{
    const RECORD_PER_PAGE = 10;

    const MESSAGE_SAVE = 'message.save';

    const MESSAGE_UPDATE = 'message.update';

    const MESSAGE_DELETE = 'message.delete';
}