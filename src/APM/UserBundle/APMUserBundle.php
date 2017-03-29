<?php

namespace APM\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class APMUserBundle extends Bundle
{
  public function getParent() {
    return 'FOSUserBundle';
  }
}
