<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\Writer\Renderer\Feed;

use Zend\Feed\Writer;
use Zend\Feed\Writer\Renderer;
use Zend\Feed\Reader;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 */
class RssTest extends \PHPUnit_Framework_TestCase
{

    protected $_validWriter = null;

    public function setUp()
    {
        $this->_validWriter = new Writer\Feed;
        $this->_validWriter->setTitle('This is a test feed.');
        $this->_validWriter->setDescription('This is a test description.');
        $this->_validWriter->setLink('http://www.example.com');

        $this->_validWriter->setType('rss');
    }

    public function tearDown()
    {
        $this->_validWriter = null;
    }

    public function testSetsWriterInConstructor()
    {
        $writer = new Writer\Feed;
        $feed   = new Renderer\Feed\Rss($writer);
        $this->assertTrue($feed->getDataContainer() instanceof Writer\Feed);
    }

    public function testBuildMethodRunsMinimalWriterContainerProperlyBeforeICheckRssCompliance()
    {
        $feed = new Renderer\Feed\Rss($this->_validWriter);
        $feed->render();
    }

    public function testFeedEncodingHasBeenSet()
    {
        $this->_validWriter->setEncoding('iso-8859-1');
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('iso-8859-1', $feed->getEncoding());
    }

    public function testFeedEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testFeedTitleHasBeenSet()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test feed.', $feed->getTitle());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedTitleIfMissingThrowsException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->remove('title');
        $rssFeed->render();
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedTitleCharDataEncoding()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->setTitle('<>&\'"áéíóú');
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getTitle());
    }

    public function testFeedDescriptionHasBeenSet()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test description.', $feed->getDescription());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedDescriptionThrowsExceptionIfMissing()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->remove('description');
        $rssFeed->render();
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedDescriptionCharDataEncoding()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->setDescription('<>&\'"áéíóú');
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getDescription());
    }

    public function testFeedUpdatedDateHasBeenSet()
    {
        $this->_validWriter->setDateModified(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getDateModified()->getTimestamp());
    }

    public function testFeedUpdatedDateIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->remove('dateModified');
        $rssFeed->render();
    }

    public function testFeedLastBuildDateHasBeenSet()
    {
        $this->_validWriter->setLastBuildDate(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getLastBuildDate()->getTimestamp());
    }

    public function testFeedGeneratorHasBeenSet()
    {
        $this->_validWriter->setGenerator('FooFeedBuilder', '1.00', 'http://www.example.com');
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('FooFeedBuilder 1.00 (http://www.example.com)', $feed->getGenerator());
    }

    public function testFeedGeneratorIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->remove('generator');
        $rssFeed->render();
    }

    public function testFeedGeneratorDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(
            'Zend_Feed_Writer ' . \Zend\Version\Version::VERSION . ' (http://framework.zend.com)', $feed->getGenerator());
    }

    public function testFeedLanguageHasBeenSet()
    {
        $this->_validWriter->setLanguage('fr');
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('fr', $feed->getLanguage());
    }

    public function testFeedLanguageIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->remove('language');
        $rssFeed->render();
    }

    public function testFeedLanguageDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testFeedIncludesLinkToHtmlVersionOfFeed()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->remove('link');
        $rssFeed->render();
    }

    public function testFeedIncludesLinkToXmlRssWhereTheFeedWillBeAvailable()
    {
        $this->_validWriter->setFeedLink('http://www.example.com/rss', 'rss');
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/rss', $feed->getFeedLink());
    }

    public function testFeedLinkToXmlRssWhereTheFeedWillBeAvailableIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $this->_validWriter->remove('feedLinks');
        $rssFeed->render();
    }

    public function testBaseUrlCanBeSet()
    {
        $this->_validWriter->setBaseUrl('http://www.example.com/base');
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/base', $feed->getBaseUrl());
    }

    /**
     * @group ZFW003
     */
    public function testFeedHoldsAnyAuthorAdded()
    {
        $this->_validWriter->addAuthor(array('name' => 'Joe',
                                             'email'=> 'joe@example.com',
                                             'uri'  => 'http://www.example.com/joe'));
        $atomFeed = new Renderer\Feed\Rss($this->_validWriter);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(array('name'=> 'Joe'), $feed->getAuthor());
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testFeedAuthorCharDataEncoding()
    {
        $this->_validWriter->addAuthor(array('name' => '<>&\'"áéíóú',
                                            'email'=> 'joe@example.com',
                                            'uri'  => 'http://www.example.com/joe'));
        $atomFeed = new Renderer\Feed\Rss($this->_validWriter);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(array('name'=> '<>&\'"áéíóú'), $feed->getAuthor());
    }

    public function testCopyrightCanBeSet()
    {
        $this->_validWriter->setCopyright('Copyright © 2009 Paddy');
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('Copyright © 2009 Paddy', $feed->getCopyright());
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testCopyrightCharDataEncoding()
    {
        $this->_validWriter->setCopyright('<>&\'"áéíóú');
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getCopyright());
    }

    public function testCategoriesCanBeSet()
    {
        $this->_validWriter->addCategories(array(
                                                array('term'   => 'cat_dog',
                                                      'label'  => 'Cats & Dogs',
                                                      'scheme' => 'http://example.com/schema1'),
                                                array('term'=> 'cat_dog2')
                                           ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            array('term'   => 'cat_dog',
                  'label'  => 'cat_dog',
                  'scheme' => 'http://example.com/schema1'),
            array('term'   => 'cat_dog2',
                  'label'  => 'cat_dog2',
                  'scheme' => null)
        );
        $this->assertEquals($expected, (array)$feed->getCategories());
    }

    /**
     * @group ZFWCHARDATA01
     */
    public function testCategoriesCharDataEncoding()
    {
        $this->_validWriter->addCategories(array(
                                                array('term'   => '<>&\'"áéíóú',
                                                      'label'  => 'Cats & Dogs',
                                                      'scheme' => 'http://example.com/schema1'),
                                                array('term'=> 'cat_dog2')
                                           ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            array('term'   => '<>&\'"áéíóú',
                  'label'  => '<>&\'"áéíóú',
                  'scheme' => 'http://example.com/schema1'),
            array('term'   => 'cat_dog2',
                  'label'  => 'cat_dog2',
                  'scheme' => null)
        );
        $this->assertEquals($expected, (array)$feed->getCategories());
    }

    public function testHubsCanBeSet()
    {
        $this->_validWriter->addHubs(
            array('http://www.example.com/hub', 'http://www.example.com/hub2')
        );
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        );
        $this->assertEquals($expected, (array)$feed->getHubs());
    }

    public function testImageCanBeSet()
    {
        $this->_validWriter->setImage(array(
                                           'uri'         => 'http://www.example.com/logo.gif',
                                           'link'        => 'http://www.example.com',
                                           'title'       => 'Image ALT',
                                           'height'      => '400',
                                           'width'       => '144',
                                           'description' => 'Image TITLE'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            'uri'         => 'http://www.example.com/logo.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image ALT',
            'height'      => '400',
            'width'       => '144',
            'description' => 'Image TITLE'
        );
        $this->assertEquals($expected, $feed->getImage());
    }

    public function testImageCanBeSetWithOnlyRequiredElements()
    {
        $this->_validWriter->setImage(array(
                                           'uri'   => 'http://www.example.com/logo.gif',
                                           'link'  => 'http://www.example.com',
                                           'title' => 'Image ALT'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = array(
            'uri'   => 'http://www.example.com/logo.gif',
            'link'  => 'http://www.example.com',
            'title' => 'Image ALT'
        );
        $this->assertEquals($expected, $feed->getImage());
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionOnMissingLink()
    {
        $this->_validWriter->setImage(array(
                                           'uri'   => 'http://www.example.com/logo.gif',
                                           'title' => 'Image ALT'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionOnMissingTitle()
    {
        $this->_validWriter->setImage(array(
                                           'uri'  => 'http://www.example.com/logo.gif',
                                           'link' => 'http://www.example.com'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionOnMissingUri()
    {
        $this->_validWriter->setImage(array(
                                           'link'  => 'http://www.example.com',
                                           'title' => 'Image ALT'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalDescriptionInvalid()
    {
        $this->_validWriter->setImage(array(
                                           'uri'         => 'http://www.example.com/logo.gif',
                                           'link'        => 'http://www.example.com',
                                           'title'       => 'Image ALT',
                                           'description' => 2
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalDescriptionEmpty()
    {
        $this->_validWriter->setImage(array(
                                           'uri'         => 'http://www.example.com/logo.gif',
                                           'link'        => 'http://www.example.com',
                                           'title'       => 'Image ALT',
                                           'description' => ''
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalHeightNotAnInteger()
    {
        $this->_validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => 'a',
                                           'width'  => 144
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalHeightEmpty()
    {
        $this->_validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '',
                                           'width'  => 144
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalHeightGreaterThan400()
    {
        $this->_validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '401',
                                           'width'  => 144
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalWidthNotAnInteger()
    {
        $this->_validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '400',
                                           'width'  => 'a'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalWidthEmpty()
    {
        $this->_validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '400',
                                           'width'  => ''
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }

    /**
     * @expectedException Zend\Feed\Writer\Exception\ExceptionInterface
     */
    public function testImageThrowsExceptionIfOptionalWidthGreaterThan144()
    {
        $this->_validWriter->setImage(array(
                                           'uri'    => 'http://www.example.com/logo.gif',
                                           'link'   => 'http://www.example.com',
                                           'title'  => 'Image ALT',
                                           'height' => '400',
                                           'width'  => '145'
                                      ));
        $rssFeed = new Renderer\Feed\Rss($this->_validWriter);
        $rssFeed->render();
    }


}
