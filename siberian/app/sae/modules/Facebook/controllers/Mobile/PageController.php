<?php

/**
 * Class Facebook_Mobile_PageController
 */
class Facebook_Mobile_PageController extends Application_Controller_Mobile_Default
{
    /**
     * Find the current page!
     */
    public function findAction ()
    {
        try {
            $optionValue = $this->getCurrentOptionValue();
            if (!$optionValue) {
                throw new Siberian_Exception(__('Missing option value.'));
            }

            $facebook = (new Facebook_Model_Facebook())
                ->find($optionValue->getId(), 'value_id');

            $app = $this->getApplication();

            $pageAndPosts = (new Facebook_Model_Page($app))
                ->get($facebook->getFbUser());

            $payload = [
                'success' => true,
                'facebook' => $pageAndPosts,
            ];
        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    public function postsAction ()
    {
        try {
            $request = $this->getRequest();
            $optionValue = $this->getCurrentOptionValue();
            if (!$optionValue) {
                throw new Siberian_Exception(__('Missing option value.'));
            }

            $facebook = (new Facebook_Model_Facebook())
                ->find($optionValue->getId(), 'value_id');

            $app = $this->getApplication();

            $after = $request->getParam('after', null);
            $posts = (new Facebook_Model_Page($app))
                ->getPosts($facebook->getFbUser(), $after);

            $payload = [
                'success' => true,
                'posts' => $posts,
            ];
        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }
}