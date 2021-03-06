<?php
require_once(dirname(__FILE__).'/../../../../bootstrap.php');

class PapayaUiToolbarPagingTest extends PapayaTestCase {

  /**
  * @covers PapayaUiToolbarPaging::__construct
  */
  public function testConstructor() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $this->assertAttributeEquals(
      new PapayaRequestParametersName('foo/page'), '_parameterName', $paging
    );
    $this->assertAttributeEquals(
      30, '_itemsCount', $paging
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::__construct
  */
  public function testConstructorWithMode() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30, PapayaUiToolbarPaging::MODE_OFFSET);
    $this->assertAttributeEquals(
      PapayaUiToolbarPaging::MODE_OFFSET, '_mode', $paging
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::setItemsCount
  * @covers PapayaUiToolbarPaging::reset
  */
  public function testSetItemsCountResetsCalculations() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $paging->papaya($this->mockPapaya()->application());
    //trigger calculation
    $dummy = $paging->currentPage;
    $paging->itemsCount = 100;
    $this->assertAttributeEquals(
      100, '_itemsCount', $paging
    );
    $this->assertAttributeSame(
      NULL, '_lastPage', $paging
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::setItemsCount
  */
  public function testSetItemsCountExpectingException() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $this->setExpectedException(
      'UnexpectedValueException',
      'UnexpectedValueException: Item count can not be negative.'
    );
    $paging->itemsCount = -42;
  }

  /**
  * @covers PapayaUiToolbarPaging::setItemsPerPage
  */
  public function testSetItemsPerPageResetsCalculations() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $paging->papaya($this->mockPapaya()->application());
    //trigger calculation
    $dummy = $paging->currentPage;
    $paging->itemsPerPage = 15;
    $this->assertAttributeEquals(
      15, '_itemsPerPage', $paging
    );
    $this->assertAttributeSame(
      NULL, '_lastPage', $paging
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::setItemsPerPage
  */
  public function testSetItemsPerPageExpectingException() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $this->setExpectedException(
      'UnexpectedValueException',
      'UnexpectedValueException: Item page limit can not be less than 1.'
    );
    $paging->itemsPerPage = 0;
  }

  /**
  * @covers PapayaUiToolbarPaging::setButtonLimit
  */
  public function testSetButtonLimitResetsCalculations() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $paging->papaya($this->mockPapaya()->application());
    //trigger calculation
    $dummy = $paging->currentPage;
    $paging->buttonLimit = 15;
    $this->assertAttributeEquals(
      15, '_buttonLimit', $paging
    );
    $this->assertAttributeSame(
      NULL, '_lastPage', $paging
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::setButtonLimit
  */
  public function testSetButtonLimitExpectingException() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $this->setExpectedException(
      'UnexpectedValueException',
      'UnexpectedValueException: Button limit can not be less than 3.'
    );
    $paging->buttonLimit = 2;
  }

  /**
  * @covers PapayaUiToolbarPaging::getCurrentPage
  * @covers PapayaUiToolbarPaging::setCurrentPage
  */
  public function testGetCurrentPageAfterSet() {
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $paging->currentPage = 3;
    $this->assertEquals(3, $paging->currentPage);
  }

  /**
  * @covers PapayaUiToolbarPaging::getCurrentPage
  * @covers PapayaUiToolbarPaging::getCurrentPageParameter
  */
  public function testGetCurrentPageFromRequest() {
    $paging = new PapayaUiToolbarPaging('page', 30);
    $paging->papaya(
      $this->mockPapaya()->application(
        array(
          'Request' => $this->mockPapaya()->request(array('page' => 3), 'http://www.test.tld/')
        )
      )
    );
    $this->assertEquals(3, $paging->currentPage);
  }

  /**
  * @covers PapayaUiToolbarPaging::getCurrentPage
  * @covers PapayaUiToolbarPaging::getCurrentPageParameter
  */
  public function testGetCurrentPageFromRequestUsingOffset() {
    $paging = new PapayaUiToolbarPaging('offset', 30, PapayaUiToolbarPaging::MODE_OFFSET);
    $paging->papaya(
      $this->mockPapaya()->application(
        array(
          'Request' => $this->mockPapaya()->request(array('offset' => 20), 'http://www.test.tld/')
        )
      )
    );
    $this->assertEquals(3, $paging->currentPage);
  }

  /**
  * @covers PapayaUiToolbarPaging::getCurrentPage
  * @covers PapayaUiToolbarPaging::getCurrentPageParameter
  */
  public function testGetCurrentPageFromRequestValidatedAndSetToMaximum() {
    $paging = new PapayaUiToolbarPaging('page', 30);
    $paging->papaya(
      $this->mockPapaya()->application(
        array(
          'Request' => $this->mockPapaya()->request(array('page' => 99), 'http://www.test.tld/')
        )
      )
    );
    $this->assertEquals(3, $paging->currentPage);
  }

  /**
  * @covers PapayaUiToolbarPaging::getCurrentPage
  * @covers PapayaUiToolbarPaging::getCurrentPageParameter
  */
  public function testGetCurrentPageExpectingOneAsDefaultValue() {
    $paging = new PapayaUiToolbarPaging('page', 30);
    $paging->papaya(
      $this->mockPapaya()->application()
    );
    $this->assertEquals(1, $paging->currentPage);
  }

  /**
  * @covers PapayaUiToolbarPaging::getCurrentPage
  */
  public function testGetCurrentPageValidatedAndSetToLastPage() {
    $paging = new PapayaUiToolbarPaging('page', 30);
    $paging->currentPage = 99;
    $this->assertEquals(3, $paging->currentPage);
  }

  /**
  * @covers PapayaUiToolbarPaging::getCurrentOffset
  * @dataProvider providePageToOffsetPairs
  */
  public function testGetCurrentOffset($page, $offset) {
    $paging = new PapayaUiToolbarPaging('page', 30);
    $paging->currentPage = $page;
    $this->assertEquals($offset, $paging->currentOffset);
  }

  /**
  * @covers PapayaUiToolbarPaging::setCurrentOffset
  * @dataProvider provideOffsetToPagePairs
  */
  public function testSetCurrentOffset($offset, $page) {
    $paging = new PapayaUiToolbarPaging('page', 30);
    $paging->currentOffset = $offset;
    $this->assertEquals($page, $paging->currentPage);
  }

  /**
  * @covers PapayaUiToolbarPaging::getLastPage
  * @dataProvider provideLastPageCalculationData
  */
  public function testLastPageCalculation($itemsPerPage, $itemsCount, $expectedMaximum) {
    $paging = new PapayaUiToolbarPaging('page', $itemsCount);
    $paging->itemsPerPage = $itemsPerPage;
    $this->assertEquals(
      $expectedMaximum, $paging->lastPage
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::appendTo
  * @covers PapayaUiToolbarPaging::appendArrowButton
  * @covers PapayaUiToolbarPaging::preparePagingParameter
  * @covers PapayaUiToolbarPaging::calculate
  */
  public function testAppendToWithAdditionalParameters() {
    $dom = new PapayaXmlDocument;
    $dom->appendElement('sample');
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $paging->papaya(
      $this->mockPapaya()->application(
        array(
          'Images' => $this->getImagesFixture()
        )
      )
    );
    $paging->currentPage = 0;
    $paging->reference()->setParameters(array('foo' => array('size' => 10)));
    $paging->appendTo($dom->documentElement);
    $this->assertEquals(
      '<sample>'.
        '<button title="1" href="http://www.test.tld/test.html?foo[page]=1&amp;foo[size]=10"'.
        ' down="down"/>'.
        '<button title="2" href="http://www.test.tld/test.html?foo[page]=2&amp;foo[size]=10"/>'.
        '<button title="3" href="http://www.test.tld/test.html?foo[page]=3&amp;foo[size]=10"/>'.
        '<button glyph="next.png" hint="Next page"'.
        ' href="http://www.test.tld/test.html?foo[page]=2&amp;foo[size]=10"/>'.
        '<button glyph="last.png" hint="Last page"'.
        ' href="http://www.test.tld/test.html?foo[page]=3&amp;foo[size]=10"/>'.
        '</sample>',
      $dom->saveXml($dom->documentElement)
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::appendTo
  * @covers PapayaUiToolbarPaging::appendArrowButton
  * @covers PapayaUiToolbarPaging::preparePagingParameter
  * @covers PapayaUiToolbarPaging::calculate
  */
  public function testAppendToWithCurrentPageEqualsTwo() {
    $dom = new PapayaXmlDocument;
    $dom->appendElement('sample');
    $paging = new PapayaUiToolbarPaging('foo/page', 30);
    $paging->papaya(
      $this->mockPapaya()->application(
        array(
          'Images' => $this->getImagesFixture()
        )
      )
    );
    $paging->currentPage = 2;
    $paging->appendTo($dom->documentElement);
    $this->assertEquals(
      '<sample>'.
        '<button glyph="previous.png" hint="Previous page"'.
        ' href="http://www.test.tld/test.html?foo[page]=1"/>'.
        '<button title="1" href="http://www.test.tld/test.html?foo[page]=1"/>'.
        '<button title="2" href="http://www.test.tld/test.html?foo[page]=2"'.
        ' down="down"/>'.
        '<button title="3" href="http://www.test.tld/test.html?foo[page]=3"/>'.
        '<button glyph="next.png" hint="Next page"'.
        ' href="http://www.test.tld/test.html?foo[page]=3"/>'.
        '</sample>',
      $dom->saveXml($dom->documentElement)
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::appendTo
  * @covers PapayaUiToolbarPaging::appendArrowButton
  * @covers PapayaUiToolbarPaging::preparePagingParameter
  * @covers PapayaUiToolbarPaging::calculate
  */
  public function testAppendToWithLimitedButton() {
    $dom = new PapayaXmlDocument;
    $dom->appendElement('sample');
    $paging = new PapayaUiToolbarPaging('foo/page', 300);
    $paging->papaya(
      $this->mockPapaya()->application(
        array(
          'Images' => $this->getImagesFixture()
        )
      )
    );
    $paging->currentOffset = 400;
    $paging->buttonLimit = 3;
    $paging->appendTo($dom->documentElement);
    $this->assertEquals(
      '<sample>'.
        '<button glyph="first.png" hint="First page"'.
        ' href="http://www.test.tld/test.html?foo[page]=1"/>'.
        '<button glyph="previous.png" hint="Previous page"'.
        ' href="http://www.test.tld/test.html?foo[page]=29"/>'.
        '<button title="28" href="http://www.test.tld/test.html?foo[page]=28"/>'.
        '<button title="29" href="http://www.test.tld/test.html?foo[page]=29"/>'.
        '<button title="30" href="http://www.test.tld/test.html?foo[page]=30" down="down"/>'.
        '</sample>',
      $dom->saveXml($dom->documentElement)
    );
  }

  /**
  * @covers PapayaUiToolbarPaging::appendTo
  * @covers PapayaUiToolbarPaging::appendArrowButton
  * @covers PapayaUiToolbarPaging::preparePagingParameter
  * @covers PapayaUiToolbarPaging::calculate
  */
  public function testAppendToWithCurrentOffsetEquals10() {
    $dom = new PapayaXmlDocument;
    $dom->appendElement('sample');
    $paging = new PapayaUiToolbarPaging('foo/offset', 30, PapayaUiToolbarPaging::MODE_OFFSET);
    $paging->papaya(
      $this->mockPapaya()->application(
        array(
          'Images' => $this->getImagesFixture()
        )
      )
    );
    $paging->currentOffset = 10;
    $paging->appendTo($dom->documentElement);
    $this->assertEquals(
      '<sample>'.
        '<button glyph="previous.png" hint="Previous page"'.
        ' href="http://www.test.tld/test.html?foo[offset]=0"/>'.
        '<button title="1" href="http://www.test.tld/test.html?foo[offset]=0"/>'.
        '<button title="2" href="http://www.test.tld/test.html?foo[offset]=10"'.
        ' down="down"/>'.
        '<button title="3" href="http://www.test.tld/test.html?foo[offset]=20"/>'.
        '<button glyph="next.png" hint="Next page"'.
        ' href="http://www.test.tld/test.html?foo[offset]=20"/>'.
        '</sample>',
      $dom->saveXml($dom->documentElement)
    );
  }

  /******************
  * Fixtures
  ******************/

  private function getImagesFixture() {
    return array(
      'actions-go-first' => 'first.png',
      'actions-go-previous' => 'previous.png',
      'actions-go-next' => 'next.png',
      'actions-go-last' => 'last.png'
    );
  }

  /******************
  * Data Provider
  ******************/

  public static function providePageToOffsetPairs() {
    return array(
      array(0, 0),
      array(1, 0),
      array(2, 10),
      array(3, 20),
      array(4, 20)
    );
  }

  public static function provideOffsetToPagePairs() {
    return array(
      array(0, 1),
      array(10, 2),
      array(20, 3),
      array(30, 3)
    );
  }

  public static function provideLastPageCalculationData() {
    return array(
      array(1, 1, 1),
      array(10, 100, 10),
      array(10, 99, 10),
      array(10, 101, 11)
    );
  }
}