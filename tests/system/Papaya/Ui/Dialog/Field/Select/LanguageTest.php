<?php
require_once(dirname(__FILE__).'/../../../../../../bootstrap.php');

class PapayaUiDialogFieldSelectLanguageTest extends PapayaTestCase {

  /**
  * @covers PapayaUiDialogFieldSelectLanguage
  */
  public function testAppendTo() {
    $select = new PapayaUiDialogFieldSelectLanguage(
      'Caption', 'name', $this->getLanguagesFixture()
    );
    $select->papaya($this->mockPapaya()->application());
    $this->assertEquals(
      '<field caption="Caption" class="DialogFieldSelectLanguage" error="yes" mandatory="yes">'.
        '<select name="name" type="dropdown">'.
          '<option value="1">Deutsch (de-DE)</option>'.
          '<option value="2">English (en-US)</option>'.
        '</select>'.
      '</field>',
      $select->getXml()
    );
  }

  /**
  * @covers PapayaUiDialogFieldSelectLanguage
  */
  public function testAppendToWithAny() {
    $select = new PapayaUiDialogFieldSelectLanguage(
      'Caption', 'name', $this->getLanguagesFixture(), PapayaUiDialogFieldSelectLanguage::OPTION_ALLOW_ANY
    );
    $select->papaya($this->mockPapaya()->application());
    $this->assertEquals(
      '<field caption="Caption" class="DialogFieldSelectLanguage" error="yes" mandatory="yes">'.
        '<select name="name" type="dropdown">'.
          '<option value="0" selected="selected">Any</option>'.
          '<option value="1">Deutsch (de-DE)</option>'.
          '<option value="2">English (en-US)</option>'.
        '</select>'.
      '</field>',
      $select->getXml()
    );
  }

  /**
  * @covers PapayaUiDialogFieldSelectLanguage
  */
  public function testAppendToWithIdentifierKeys() {
    $select = new PapayaUiDialogFieldSelectLanguage(
      'Caption', 'name', $this->getLanguagesFixture(), PapayaUiDialogFieldSelectLanguage::OPTION_USE_IDENTIFIER
    );
    $select->papaya($this->mockPapaya()->application());
    $this->assertEquals(
      '<field caption="Caption" class="DialogFieldSelectLanguage" error="yes" mandatory="yes">'.
        '<select name="name" type="dropdown">'.
          '<option value="de">Deutsch (de-DE)</option>'.
          '<option value="en">English (en-US)</option>'.
        '</select>'.
      '</field>',
      $select->getXml()
    );
  }

  /**
  * @covers PapayaUiDialogFieldSelectLanguage
  */
  public function testAppendToWithIdentifierKeysAndAny() {
    $select = new PapayaUiDialogFieldSelectLanguage(
      'Caption',
      'name',
      $this->getLanguagesFixture(),
      PapayaUiDialogFieldSelectLanguage::OPTION_USE_IDENTIFIER |
      PapayaUiDialogFieldSelectLanguage::OPTION_ALLOW_ANY
    );
    $select->papaya($this->mockPapaya()->application());
    $this->assertEquals(
      '<field caption="Caption" class="DialogFieldSelectLanguage" error="yes" mandatory="yes">'.
        '<select name="name" type="dropdown">'.
          '<option value="*">Any</option>'.
          '<option value="de">Deutsch (de-DE)</option>'.
          '<option value="en">English (en-US)</option>'.
        '</select>'.
      '</field>',
      $select->getXml()
    );
  }

  /**
   * @return PHPUnit_Framework_MockObject_MockObject|PapayaContentLanguages
   */
  private function getLanguagesFixture() {
    $languages = $this->getMock('PapayaContentLanguages');
    $languages
      ->expects($this->any())
      ->method('getIterator')
      ->will(
        $this->returnValue(
          new ArrayIterator(
            array(
              1 => array(
                'identifier' => 'de',
                'code' => 'de-DE',
                'title' => 'Deutsch'
              ),
              2 => array(
                'identifier' => 'en',
                'code' => 'en-US',
                'title' => 'English'
              )
            )
          )
        )
      );
    return $languages;
  }
}
