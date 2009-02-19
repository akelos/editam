<?php

require_once(AK_LIB_DIR.DS.'AkActionController.php');
require_once(AK_LIB_DIR.DS.'AkRequest.php');
require_once(AK_LIB_DIR.DS.'AkResponse.php');
require_once(AK_LIB_DIR.DS.'AkActionView.php');
require_once(AK_LIB_DIR.DS.'AkActionView'.DS.'AkActionViewHelper.php');
require_once(AK_APP_DIR.DS.'helpers'.DS.'editags_helper.php');

Ak::import('page,page_part,content_layout,editam_filter');

define('EDITAM_AVAILABLE_FILTERS','markdown,smartypants,textile');

Mock::generate('AkActionController');
Mock::generate('AkRequest');
Mock::generate('AkResponse');
Mock::generate('AkActionView');
Mock::generate('EditagsHelper');


// Page without nested for creating initial pages from yaml data
class TestingPage extends Page
{
    var $__testing_mode = true;
    function getModelName(){
        return 'Page';
    }
    function getTableName(){
        return 'pages';
    }
}


class PageTestCase extends AkUnitTest
{
    function setup()
    {
        if(empty($this->Page)){
            require_once(AK_APP_DIR.DS.'installers'.DS.'editam_installer.php');
            $installer = new EditamInstaller();
            $installer->do_not_create_sample_data = true;
            $installer->uninstall();

            $installer->install();

            $Pages = Ak::convert('yaml','array', Ak::file_get_contents(AK_TEST_DIR.DS.'fixtures'.DS.'db'.DS.'pages.yaml'));
            $this->_loadPage();

            $Layouts = Ak::convert('yaml','array', Ak::file_get_contents(AK_TEST_DIR.DS.'fixtures'.DS.'db'.DS.'content_layouts.yaml'));
            $this->Layout =& new ContentLayout();
            foreach ($Layouts as $Layout){
                $this->Layout->create($Layout);
            }

            $this->Page =& new TestingPage();
            foreach ($Pages as $Page){
                $Page =& new TestingPage($Page);
                $Page->save();
            }

            $PageParts = Ak::convert('yaml','array', Ak::file_get_contents(AK_TEST_DIR.DS.'fixtures'.DS.'db'.DS.'page_parts.yaml'));
            $this->PagePart =& new PagePart();
            foreach ($PageParts as $PagePart){
                $this->PagePart->create($PagePart);
            }

        }

        $this->_loadPage();
    }

    function _loadPage()
    {
        $this->Page =& new Page();
        $this->_loadPageDependencies($this->Page);
    }

    function _loadPageDependencies(&$Page)
    {
        $Controller =& new MockAkActionController();
        $Controller->Request =& new MockAkRequest();
        $Controller->Response =& new MockAkResponse();
        $Controller->Template =& new MockAkActionView();
        $Controller->editags_helper =& new MockEditagsHelper();
        $Controller->editags_helper->_controller =& $Controller;
        $Controller->Page =& $Page;
        $Page->initiateBehaviour($Controller);
    }


    function test_page_slug_validation()
    {
        $valid = array('abc', 'abcd-efg', 'abcd_efg', 'abc.html', '/', '123','good-slug.html','a');
        foreach ($valid as $slug){
            $Page =& $this->Page->create(array('slug'=>$slug));
            $this->assertNoPattern('/is invalid/',$Page->getErrorsOn('slug'));
        }

        return;

        $invalid = array('-acb-','ab--cc,','ab-.cc','ab..cc','ab.','invalid format', 'abcd efg', ' abcd', 'abcd/efg','eñe');
        foreach ($invalid as $slug){
            $Page =& $this->Page->create(array('slug'=>$slug));
            $this->assertPattern('/is invalid/',$Page->getErrorsOn('slug'));
        }
    }

    function test_validates_uniqueness_of_slug_on_node()
    {
        $Page =& $this->Page->create(array('slug'=>'contact','parent_id'=>2));
        $this->assertPattern('/is already used/',$Page->getErrorsOn('slug'));
    }

    function test_finding_by_url()
    {
        $Page =& $this->Page->findByUrl('/');
        $this->assertEqual($Page->get('title'), 'Home Page');

        $Page =& $this->Page->findByUrl('/about');
        $this->assertEqual($Page->get('title'), 'About');

        $Page =& $this->Page->findByUrl('/docs/');
        $this->assertEqual($Page->get('title'), 'Documentation');

        $Page =& $this->Page->findByUrl('/docs/tutorials');
        $this->assertEqual($Page->get('title'), 'Tutorials');
    }

    function test_finding_by_url_should_not_show_other_than_published()
    {
        $this->assertFalse($this->Page->findByUrl('/about/our-team'));
        $this->assertFalse($this->Page->findByUrl('/about/our-team/bermi'));
    }

    function test_finding_by_url_shows_published_if_requested()
    {
        $Page =& $this->Page->findByUrl('/about/our-team',true);
        $this->assertEqual($Page->get('title'), 'Our team');

        $Page =& $this->Page->findByUrl('/about/our-team/bermi',true);
        $this->assertEqual($Page->get('title'), 'Bermi');
    }

    function  test_validates_presence_of()
    {
        foreach (array('title', 'slug', 'breadcrumb') as $field){
            $Page =& $this->Page->create(array());
            $this->assertNoPattern('/required/',$Page->getErrorsOn($field));
        }
    }

    function test_page_should_return_its_parts()
    {
        $Page =& $this->Page->findByUrl('/');
        $Body =& $Page->getPart('body');

        $this->assertEqual($Body->get('content'), 'Home page body');

        $SiteMenu =& $Page->getPart('menu');
        $this->assertEqual($SiteMenu->get('content'), 'site menu');

        $this->assertEqual($Page->getPart('invalid'), false);

        $Page =& $this->Page->findByUrl('/docs');
        $Body =& $Page->getPart('body');
        $this->assertEqual($Body->get('content'), 'Documentation body');

        $SiteMenu =& $Page->getPart('menu');
        $this->assertPattern('/documentation menu/', $SiteMenu->get('content'));
    }

    function test_page_should_return_its_parts_content()
    {
        $Page =& $this->Page->findByUrl('/');
        $this->assertEqual($Page->getFilteredPart('body'), 'Home page body');
        $this->assertEqual($Page->getFilteredPart('menu'), 'site menu');
        $this->assertEqual($Page->getFilteredPart('invalid'), false);

        $Page =& $this->Page->findByUrl('/docs');
        $this->assertEqual($Page->getFilteredPart('body'), 'Documentation body');

        $SiteMenu =& $Page->getPart('menu');
        $this->assertEqual($Page->getFilteredPart('menu'), "documentation menu {%= pages_list :first = '/docs', :id = 'menu', :class = 'menu docs' %}");
    }


    function test_page_should_return_inherited_parts()
    {
        $Page =& $this->Page->findByUrl('/about');
        $Body =& $Page->getInheritedPart('body');
        $this->assertEqual($Body->get('content'), 'About body');
        $SiteMenu =& $Page->getInheritedPart('menu');
        $this->assertEqual($SiteMenu->get('content'), 'site menu');

        $Page =& $this->Page->findByUrl('/docs/tutorials');
        $Body =& $Page->getInheritedPart('body');
        $this->assertEqual($Body->get('content'), 'Tutorials body');
        $SiteMenu =& $Page->getInheritedPart('menu');
        $this->assertEqual($SiteMenu->get('content'), "documentation menu {%= pages_list :first = '/docs', :id = 'menu', :class = 'menu docs' %}");
    }


    function test_page_should_return_inherited_parts_content()
    {
        $Page =& $this->Page->findByUrl('/about');
        $this->assertEqual($Page->getFilteredPart('body', true), 'About body');
        $this->assertEqual($Page->getFilteredPart('menu', true), 'site menu');

        $Page =& $this->Page->findByUrl('/docs/tutorials');
        $this->assertEqual($Page->getFilteredPart('body', true), 'Tutorials body');
        $this->assertEqual($Page->getFilteredPart('menu', true), "documentation menu {%= pages_list :first = '/docs', :id = 'menu', :class = 'menu docs' %}");
    }

    function test_page_should_return_part_formatted_as_markdown()
    {
        $Page =& $this->Page->findByUrl('/');
        $this->assertEqual($Page->getFilteredPart('footer'), '<p>markdown <em>footer</em></p>');

        $Page =& $this->Page->findByUrl('/docs/tutorials');
        $this->assertEqual($Page->getFilteredPart('footer', true), '<p>markdown <em>footer</em></p>');
    }

    function test_page_should_return_part_formatted_as_textile()
    {
        $Page =& $this->Page->findByUrl('/docs/tutorials/creating-a-bookstore/introduction', true);
        $this->assertEqual($Page->getFilteredPart('body'), '<h2>Introduction</h2>');
    }

    function test_published_at_date_should_be_updated_when_status_is_changed_to_published()
    {
        $Page =& $this->Page->findByUrl('/docs/tutorials/creating-a-bookstore', true);
        $this->assertEqual($Page->get('published_at'), null);
        $Page->status = 'published';
        $this->assertTrue($Page->save());
        $this->assertEqual($Page->get('published_at'), Ak::getDate());

    }

    function test_published_at_not_updated_on_save_because_already_published()
    {
        $Page =& $this->Page->findByUrl('/');
        $this->assertEqual($Page->get('published_at'), '2000-01-25 16:00:00');

        $Page->title = 'Home';
        $this->assertTrue($Page->save());

        $Page =& $this->Page->findByUrl('/');
        $this->assertEqual($Page->get('published_at'), '2000-01-25 16:00:00');

        $Page->status = 'reviewed';
        $this->assertTrue($Page->save());
        $Page =& $this->Page->findByUrl('/', true);
        $this->assertEqual($Page->get('published_at'), '2000-01-25 16:00:00');

        $Page->status = 'published';
        $this->assertTrue($Page->save());
        $Page =& $this->Page->findByUrl('/', true);
        $this->assertEqual($Page->get('published_at'), '2000-01-25 16:00:00');

    }


    function test_page_should_return_current_layout()
    {
        $Page =& $this->Page->findByUrl('/');
        $this->assertPattern('/base_template/',$Page->getLayout());

        $Page =& $this->Page->findByUrl('/docs');
        $this->assertPattern('/docs_template/',$Page->getLayout());
    }


    function test_page_should_return_inherited_layout()
    {
        $Page =& $this->Page->findByUrl('/about');
        $Layout =& $Page->getLayoutInstance();
        $this->assertPattern('/base_template/',$Layout->get('content'));

        $Page =& $this->Page->findByUrl('/docs/tutorials');
        $Layout =& $Page->getLayoutInstance();
        $this->assertPattern('/docs_template/',$Layout->get('content'));
    }


    function test_should_return_part_instance()
    {
        $Page =& $this->Page->findByUrl('docs');
        $this->_loadPageDependencies($Page);
        $Part = $Page->getPart('menu');
        $this->assertEqual($Part->get('content'), "documentation menu {%= pages_list :first = '/docs', :id = 'menu', :class = 'menu docs' %}");
    }

    function test_should_return_layout()
    {
        $Page =& $this->Page->findByUrl('docs');
        $this->_loadPageDependencies($Page);
        $this->assertPattern('/<title>Docs/',  $Page->getLayout());
    }

    function test_should_return_filtered_body()
    {
        $Page =& $this->Page->findByUrl('docs');
        $this->_loadPageDependencies($Page);
        $this->assertEqual($Page->getFilteredPart('body'), 'Documentation body');

    }



    function test_destroy_dependent_parts()
    {
        $Page =& $this->Page->findByUrl('docs/api');
        $Page->part->load();

        $part_id = $Page->parts[0]->getId();
        $this->assertTrue($Page->destroy());
        $this->assertFalse($this->PagePart->find($part_id));
    }


    function test_destroy_nested_elements_and_their_dependent_parts()
    {

        $Page =& $this->Page->findByUrl('/docs/tutorials/creating-a-bookstore/introduction', true);
        $outermost_page_id = $Page->getId();
        $Page->part->load();
        $outermost_part_id = $Page->parts[0]->getId();

        $Page =& $this->Page->findByUrl('/docs/tutorials/creating-a-bookstore', true);
        $child_page_id = $Page->getId();
        $Page->part->load();
        $child_part_id = $Page->parts[0]->getId();

        $Page =& $this->Page->findByUrl('docs/tutorials');
        $Page->part->load();
        $part_id = $Page->parts[0]->getId();

        $this->assertTrue($Page->destroy());

        $this->assertFalse($this->PagePart->find($part_id));
        $this->assertFalse($this->PagePart->find($child_part_id));
        $this->assertFalse($this->PagePart->find($outermost_part_id));

        $this->assertFalse($this->Page->find($child_page_id));
        $this->assertFalse($this->Page->find($outermost_page_id));
    }

    function test_should_not_allow_changing_page_locale()
    {
        $Page =& $this->Page->findByUrl('/about/our-team/bermi',true);
        $this->assertEqual($Page->get('locale'), 'en');

        $Page->set('locale','es');

        $this->assertFalse($Page->save());

        $this->assertPattern('/change/', $Page->getErrorsOn('locale'));

        $Page =& $this->Page->findByUrl('/about/our-team/bermi',true);
        $this->assertEqual($Page->get('locale'), 'en');

    }
}


?>
