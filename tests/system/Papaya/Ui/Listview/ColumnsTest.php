<?php
require_once(dirname(__FILE__).'/../../../../bootstrap.php');

class PapayaUiListviewColumnsTest extends PapayaTestCase {

  /**
  * @covers PapayaUiListviewColumns::__construct
  * @covers PapayaUiListviewColumns::owner
  */
  public function testConstructor() {
    $listview = $this->getMock('PapayaUiListview');
    $columns = new PapayaUiListviewColumns($listview);
    $this->assertSame(
      $listview, $columns->owner()
    );
  }
}