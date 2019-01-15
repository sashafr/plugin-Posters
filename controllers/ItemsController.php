<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2014
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Posters
 */

class Posters_ItemsController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Item');
        $this->_browseRecordsPerPage = 10;
    }

    public function browseAction()
    {
        if ($this->_getParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
            $this->_setParam('sort_dir',   'd');
        }
        
        $this->view->posters = $this->_helper->db->findBy(array('user_id' => $this->_currentUser->id), 'Poster');

        //Must be logged in to view items specific to certain users
        /*if ($this->_getParam('user') && !$this->_helper->acl->isAllowed('browse', 'users')) {
            $this->_helper->flashMessenger('May not browse by specific users');
            $this->_setParam('user',null);
        }*/

        parent::browseAction();
    }

    public function addAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    public function editAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    public function deleteAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
}
