<?php
/**
 * Poster Builder 
 *
 * @copyright Copyright 2008-2013 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Poster index controller class
 *
 * @package Posters
 */
class Posters_PostersController extends Omeka_Controller_AbstractActionController
{
    const UNTITLED_POSTER = 'Untitled';

    public function init()
    {
       $this->_helper->db->setDefaultModelName('Poster');
       $this->_currentUser = current_user();
    }
    public function indexAction()
    {
        $this->_helper->redirector('browse','index');
    }
    
    public function browseAction() {
       
            parent::browseAction();
    }
    
    public function editAction() {
       //get the poster object
        $poster = $this->_helper->db->findById(null, 'Poster');
        //$this->_verifyAccess($poster,'edit');
        //retrieve public items 
        $items = $this->_helper->db->getTable()->findByUserId($this->_currentUser->id);
        //var_dump($items); exit;
        $this->view->assign(compact('poster','items'));
    }
    public function showAction() {
        $params = $this->getRequest()->getParams();
     
        $poster = $this->_helper->db->findById(null, 'Poster');        
         
        $this->view->poster = $poster;
    }
    public function newAction(){

        $poster = new Poster();
        $poster->title = self::UNTITLED_POSTER;
        $poster->user_id = $this->_currentUser->id;
        $poster->description = '';
        $poster->date_created = date('Y-m-d H:i:s', time());
        $poster->save();
        
        //Set the new poster id for discard
        $_SESSION['new_poster_id'] = $poster->id;
         
         $bp = get_option('poster_page_path'); 
        $this->_helper->redirector->gotoRoute(
            array(
                'controller' => 'posters',
                'module' => 'posters',
                'action' => 'edit',
                'id'     => $poster->id
            ),
            "$bp"
        );

    }
    public function saveAction()
    {
        // clear the new poster id for didscard
        unset($_SESSION['new_poster_id']);
        $poster = $this->_helper->db->findById(null, 'Poster');

        $params = $this->getRequest()->getParams();
        $poster->title = !empty($params['title']) ? $params['title'] : self::UNTITLED_POSTER;
        $poster->description = $params['description'];
        $poster->updateItems($params);
        $poster->save();

        //$this->flashSuccess("\"$poster->title\" was successfully saved.");

        /*if(is_admin_theme()) {*/
            $this->_helper->redirector->gotoRoute(
                array(
                    'module' => 'posters',
                    'action' => 'browse'
                ),
                'default'
            );
    }
    
    public function deleteAction()
    {
        echo "Delete Action"; exit;
    }
    public function helpAction(){

    }
    
    public function discardAction()
    {
        if (isset($_SESSION['new_poster_id'])) {
            // if the poster was just created and 
            // not yet saved by the edit  form,
            // then delete it.
            $poster = $this->_helper->db->findById($_SESSION['new_poster_id'], 'Poster');
            //check to make sure the poster belongs to the logged in user
          $this->_verifyAccess($poster, 'delete');
            //delete the poster
            $poster->delete();
            //Clear the new Poster id for discard
            unset($_SESSION['new_poster_id']);
        }
        
        if(is_admin_theme()) {
            $this->_helper->redirector->gotoRoute(array('action' => 'browse'), 'default');
        } else {
            $this->_helper->redirector->gotoUrl('guest-user/user/me');
        }
       
    }
    
    protected function _verifyAccess($poster, $action)
    {
        /*
         * Blog access for users who didn't make the poster,
         * or people who don't have permission.
         */
        if($poster->user_id != $this->_currentUser->id 
                and !$this->_helper->acl->isAllowed($action. 'Any')) {
            throw new Omeka_Controller_Exception_403;
        }
    }
}