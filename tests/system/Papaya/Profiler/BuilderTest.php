<?php
require_once(dirname(__FILE__).'/../../../bootstrap.php');

class PapayaProfilerBuilderTest extends PapayaTestCase {

  /**
  * @covers PapayaProfilerBuilder::createCollector
  */
  public function testCreateCollector() {
    $builder = new PapayaProfilerBuilder();
    $this->assertInstanceOf('PapayaProfilerCollectorXhprof', $builder->createCollector());
  }

  /**
  * @covers PapayaProfilerBuilder::createStorage
  */
  public function testCreateStorageExpectFile() {
    $builder = new PapayaProfilerBuilder();
    $builder->papaya(
      $this->mockPapaya()->application(
        array(
          'options' => $this->mockPapaya()->options(
            array('PAPAYA_PROFILER_STORAGE_DIRECTORY' => $this->createTemporaryDirectory())
          )
        )
      )
    );
    $storage = $builder->createStorage();
    $this->removeTemporaryDirectory();
    $this->assertInstanceOf('PapayaProfilerStorageFile', $storage);
  }

  /**
  * @covers PapayaProfilerBuilder::createStorage
  */
  public function testCreateStorageExpectXhgui() {
    $builder = new PapayaProfilerBuilder();
    $builder->papaya(
      $this->mockPapaya()->application(
        array(
          'options' => $this->mockPapaya()->options(
            array(
              'PAPAYA_PROFILER_STORAGE' => 'xhgui'
            )
          )
        )
      )
    );
    $storage = $builder->createStorage();
    $this->assertInstanceOf('PapayaProfilerStorageXhgui', $storage);
  }
}