<?php

/**
 * Class Wordpress2_Mobile_ListController
 */
class Wordpress2_Mobile_ListController extends Application_Controller_Mobile_Default
{
    /**
     *
     */
    public function findAction()
    {
        try {
            $optionValue = $this->getCurrentOptionValue();
            $valueId = $optionValue->getId();
            $request = $this->getRequest();
            $page = $request->getParam('page', 1);
            $refresh = filter_var($request->getParam('refresh', false), FILTER_VALIDATE_BOOLEAN);

            $wordpress = (new Wordpress2_Model_Wordpress())
                ->find($valueId, 'value_id');

            if (!$wordpress->getId()) {
                throw new Siberian_Exception('#33-001: ' . __('An error occured.'));
            }

            $cacheId = 'wordpress2_find_' . $valueId . '_page_' . $page;
            $cacheTag = 'wordpress2_find_' . $valueId;
            $result = $this->cache->load($cacheId);
            if (!$result || $refresh) {

                // Clear subsequents pages if pull to refresh called!
                if ($refresh) {
                    $this->cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, [
                        $cacheTag,
                        'wordpress2',
                        'findAction',
                    ]);
                }

                $wordpressData = [
                    'url' => $wordpress->getData('url'),
                    'picture' => $wordpress->getData('picture'),
                    'showCover' => (boolean) $wordpress->getData('show_cover'),
                    'groupQueries' => (boolean) $wordpress->getData('group_queries'),
                    'cardDesign' => (boolean) $wordpress->getData('card_design'),
                ];

                // Fetch queries!
                $wordpressQueries = (new Wordpress2_Model_Query())
                    ->findAll(
                        [
                            'value_id' => $valueId,
                            'is_published' => 1
                        ]
                    );
                $queries = [];
                $categoryIds = [];
                foreach ($wordpressQueries as $wordpressQuery) {
                    $query = Siberian_Json::decode($wordpressQuery->getData('query'));
                    $queryId = $wordpressQuery->getId();
                    $queries[] = [
                        'id' => $queryId,
                        'title' => $wordpressQuery->getData('title'),
                        'subtitle' => $wordpressQuery->getData('subtitle'),
                        'picture' => $wordpressQuery->getData('picture'),
                        'thumbnail' => $wordpressQuery->getData('thumbnail'),
                        'showCover' => (boolean) $wordpressQuery->getData('show_cover'),
                        'query' => $query,
                        'position' => $wordpressQuery->getData('position'),
                    ];

                    $categoryIds[$queryId] = $query['categories'];
                }

                $wordpressApi = (new Wordpress2_Model_WordpressApi())
                    ->init(
                        $wordpress->getData('url'),
                        $wordpress->getData('login'),
                        $wordpress->getData('password')
                    );

                $posts = [];

                // Immediate fetch 20 first rows 'grouped'
                if ($wordpressData['groupQueries']) {
                    $groupIds = [];
                    foreach ($categoryIds as $queryId => $categories) {
                        $groupIds += $categories;
                    }
                    $posts = $wordpressApi->getPosts(
                        implode(',', array_values($groupIds)),
                        $page
                    );
                }

                $payload = [
                    'success' => true,
                    'page_title' => $optionValue->getTabbarName(),
                    'queries' => $queries,
                    'wordpress' => $wordpressData,
                    'posts' => $posts,
                ];

                $cacheLifetime = $wordpress->getData('cache_lifetime');
                if ($cacheLifetime === 'null') {
                    $cacheLifetime = null;
                }

                $this->cache->save(Siberian_Json::encode($payload), $cacheId, [
                    'wordpress2',
                    'findAction',
                    'value_id_' . $valueId,
                    $cacheTag
                ], $cacheLifetime);

                $payload['x-cache'] = 'MISS';
            } else {
                $payload = Siberian_Json::decode($result);
                $payload['x-cache'] = 'HIT';
            }

        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     *
     */
    public function loadpostsAction()
    {
        try {
            $optionValue = $this->getCurrentOptionValue();
            $valueId = $optionValue->getId();
            $request = $this->getRequest();
            $page = $request->getParam('page', 1);
            $refresh = filter_var($request->getParam('refresh', false), FILTER_VALIDATE_BOOLEAN);
            $queryId = $request->getParam('queryId', null);

            $wordpress = (new Wordpress2_Model_Wordpress())
                ->find($valueId, 'value_id');

            if (!$wordpress->getId()) {
                throw new Siberian_Exception('#33-002: ' . __('An error occured.'));
            }

            $cacheId = 'wordpress2_loadposts_' . $valueId . '_query_' . $queryId . '_page_' . $page;
            $cacheTag = 'wordpress2_loadposts_' . $valueId . '_query_' . $queryId;
            $result = $this->cache->load($cacheId);
            if (!$result || $refresh) {

                // Clear subsequents pages if pull to refresh called!
                if ($refresh) {
                    $this->cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, [
                        $cacheTag,
                        'wordpress2',
                        'loadpostsAction',
                    ]);
                }

                // Fetch query!
                $wordpressQuery = (new Wordpress2_Model_Query())
                    ->find(
                        [
                            'query_id' => $queryId,
                            'is_published' => 1
                        ]
                    );

                if (!$wordpressQuery->getId()) {
                    throw new Siberian_Exception('#33-003: ' . __('An error occured.'));
                }

                $query = Siberian_Json::decode($wordpressQuery->getData('query'));
                $categoryIds = $query['categories'];
                $queryData = [
                    'title' => $wordpressQuery->getData('title'),
                    'subtitle' => $wordpressQuery->getData('subtitle'),
                    'picture' => $wordpressQuery->getData('picture'),
                    'thumbnail' => $wordpressQuery->getData('thumbnail'),
                    'showCover' => (boolean)$wordpressQuery->getData('show_cover'),
                    'showTitle' => (boolean)$wordpressQuery->getData('show_title'),
                    'query' => $query,
                    'position' => $wordpressQuery->getData('position'),
                ];

                $wordpressData = [
                    'url' => $wordpress->getData('url'),
                    'picture' => $wordpress->getData('picture'),
                    'showCover' => (boolean)$wordpress->getData('show_cover'),
                    'groupQueries' => (boolean)$wordpress->getData('group_queries'),
                    'cardDesign' => (boolean)$wordpress->getData('card_design'),
                ];

                $wordpressApi = (new Wordpress2_Model_WordpressApi())
                    ->init(
                        $wordpress->getData('url'),
                        $wordpress->getData('login'),
                        $wordpress->getData('password')
                    );

                $posts = $wordpressApi->getPosts(
                    implode(',', array_values($categoryIds)),
                    $page
                );

                $payload = [
                    'success' => true,
                    'page_title' => $optionValue->getTabbarName(),
                    'query' => $queryData,
                    'wordpress' => $wordpressData,
                    'posts' => $posts,
                ];

                $cacheLifetime = $wordpress->getData('cache_lifetime');
                if ($cacheLifetime === 'null') {
                    $cacheLifetime = null;
                }

                $this->cache->save(Siberian_Json::encode($payload), $cacheId, [
                    'wordpress2',
                    'loadpostsAction',
                    'value_id_' . $valueId,
                    $cacheTag
                ], $cacheLifetime);

                $payload['x-cache'] = 'MISS';
            } else {
                $payload = Siberian_Json::decode($result);
                $payload['x-cache'] = 'HIT';
            }
        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }
}