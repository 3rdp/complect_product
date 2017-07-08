<?php

// This is specific to this component
define('JPATH_COMPONENT', JPATH_BASE . "/plugins/jshoppingproducts/complect_product/");

// Include dependancies
require_once JPATH_COMPONENT . 'complect_product.php';

use PHPUnit\Framework\TestCase;

class ComplectProductTest extends TestCase {
  function testOnBeforeDisplayProductView() {
    $this->assertEquals("abc", "abc");
  } 
}
