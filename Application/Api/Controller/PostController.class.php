<?php
namespace Api\Controller;

use Api\Controller\BaseController;

/**
 * 文章API控制器。
 */
class PostController extends BaseController
{
    // REST允许的请求类型列表
    protected $allowMethod = array('get', 'post', 'put', 'delete');

    // REST允许请求的资源类型列表
    protected $allowType = array('json');

    // 默认的资源类型
    protected $defaultType = 'json';

    /**
     * 文章列表。
     * @return void
     */
    public function index()
    {
        $msg = PHP_EOL . 'Api\Controller\PostController::index():';

        // $msg .= PHP_EOL . '路径常量:'
        //     . PHP_EOL . '  MODULE_PATH = ' . MODULE_PATH
        //     . PHP_EOL . '  CONTROLLER_PATH = ' . CONTROLLER_PATH
        //     . PHP_EOL . '系统常量:'
        //     . PHP_EOL . '  __APP__ = ' . __APP__
        //     . PHP_EOL . '  __MODULE__ = ' . __MODULE__
        //     . PHP_EOL . '  __CONTROLLER__ = ' . __CONTROLLER__
        //     . PHP_EOL . '  __ACTION__ = ' . __ACTION__
        //     . PHP_EOL . '  __SELF__ = ' . __SELF__
        //     . PHP_EOL . '  __INFO__ = ' . __INFO__
        //     . PHP_EOL . '  MODULE_NAME = ' . MODULE_NAME
        //     . PHP_EOL . '  CONTROLLER_NAME = ' . CONTROLLER_NAME
        //     . PHP_EOL . '  ACTION_NAME = ' . ACTION_NAME
        //     . PHP_EOL . '  REQUEST_METHOD = ' . REQUEST_METHOD
        //     . PHP_EOL . '配置:'
        //     . PHP_EOL . '  C(URL_ROUTER_ON) = ' . C('URL_ROUTER_ON')
        //     . PHP_EOL . '  C(URL_MODEL) = ' . C('URL_MODEL')
        //     . PHP_EOL . '  C(URL_HTML_SUFFIX) = ' . C('URL_HTML_SUFFIX');
        // $msg .= PHP_EOL . str_repeat('-', 80);
        // trace($msg, '调试', 'DEBUG', true);

        try {
            $filter = I('filter');
            $order = I('order');
            $page = I(C('VAR_PAGE'));
            $pageSize = I('pageSize');
            $parameters = compact('filter', 'order', 'page', 'pageSize');

            $msg .= // PHP_EOL . '  VAR_PAGE = ' . C('VAR_PAGE') .
                PHP_EOL . '  parameters = ' . print_r($parameters, true);

            $Post = D('Home/Post');
            $posts = $Post->paginate($parameters);

            // $msg .= PHP_EOL . '  $posts = ' . print_r($posts, true);

            $this->response($posts, 'json');
        } catch (Exception $e) {
            $msg .= PHP_EOL . 'error: ' . $e->getMessage();
            throw $e;
        } finally {
            $msg .= PHP_EOL . str_repeat('-', 80);
            // \Think\Log::write($msg, 'DEBUG');
        }
    }

    /**
     * 添加文章。
     * @return void
     */
    public function create()
    {
        if (!IS_POST) {
            $this->response(L('METHOD_NOT_ALLOWED'), 'json', 405);
            return;
        }

        $msg = PHP_EOL . 'Api\Controller\PostController::create():';

        try {
            $input = $this->getPostInput();

            $msg .= PHP_EOL . '  $input = ' . print_r($input, true);

            $Post = D('Home/Post');
            $newPost = $Post->create($input);

            $msg .= PHP_EOL . '  $newPost = ' . print_r($newPost, true);

            if (!$newPost) {
                $msg .= PHP_EOL . '  validation error: ' . $Post->getError();

                $data = [
                    'data' => $input,
                    'validationError' => $Post->getError(),
                ];
                $this->response($data, 'json', 400);
            } else {
                $result = $Post->add();

                $msg .= PHP_EOL . '  $result = ' . print_r($result, true);

                if ($result !== false) {
                    $data = [
                        'post' => $Post->find($result),
                    ];
                    $meta = [
                        'message' => L('SAVE_POST_SUCCESS'),
                    ];

                    $msg .= PHP_EOL . '  $data = ' . print_r($data, true);
                    $msg .= PHP_EOL . '  $meta = ' . print_r($meta, true);
                    $msg .= PHP_EOL . str_repeat('-', 80);
                    \Think\Log::write($msg, 'DEBUG');

                    $this->response(compact('data', 'meta'), 'json', 201);
                } else {
                    $meta = [
                        'message' => L('SAVE_POST_FAILURE'),
                    ];

                    $msg .= PHP_EOL . '  $meta = ' . print_r($meta, true);
                    $msg .= PHP_EOL . str_repeat('-', 80);
                    \Think\Log::write($msg, 'DEBUG');

                    $this->response(compact('meta'), 'json', 500);
                }
            }
        } catch (Exception $e) {
            $msg .= PHP_EOL . '  error: ' . $e->getMessage();
            throw $e;
        } finally {
            $msg .= PHP_EOL . str_repeat('-', 80);
            \Think\Log::write($msg, 'DEBUG');
        }
    }

    /**
     * 读取文章。
     * @param int $id 文章id
     * @return void
     */
    public function show($id)
    {
        if (!IS_GET) {
            $this->response(L('METHOD_NOT_ALLOWED'), 'json', 405);
            return;
        }

        $msg = PHP_EOL . 'Api\Controller\PostController::show():'
            . PHP_EOL . '  $id = ' . $id;

        $Post = D('Home/Post');
        $post = $Post->relation(true)->find($id);

        $msg .= PHP_EOL . '  $post = ' . print_r($post, true);

        if (!$post) {
            $msg .= PHP_EOL . '  ' . L('POST_NOT_FOUND');

            $this->response(L('POST_NOT_FOUND'), 'json', 400);
            return;
        }

        $data = compact('post');
        $Post->protect($data);

        $msg .= PHP_EOL . '  $data = ' . print_r($data, true);
        $msg .= PHP_EOL . str_repeat('-', 80);
        // \Think\Log::write($msg, 'DEBUG');

        $this->response($data, 'json', 200);
    }

    /**
     * 修改文章。
     * @param int $id 文章id
     * @return void
     */
    public function update($id)
    {
        if (!IS_POST) {
            $this->response(L('METHOD_NOT_ALLOWED'), 'json', 405);
            return;
        }

        $msg = PHP_EOL . 'Api\Controller\PostController::update():'
            . PHP_EOL . '  $id = ' . $id;

        try {
            $Post = D('Home/Post');

            $oldPost = $Post->find($id);

            $msg .= PHP_EOL . '  $oldPost = ' . print_r($oldPost, true);

            if (!$oldPost) {
                $msg .= PHP_EOL . '  ' . L('POST_NOT_FOUND');

                $this->response(L('POST_NOT_FOUND'), 'json', 400);
                return;
            }

            $input = $this->getPostInput();

            $msg .= PHP_EOL . '  $input = ' . print_r($input, true);

            $updatedPost = $Post->create($input);

            $msg .= PHP_EOL . '  $updatedPost = ' . print_r($updatedPost, true);

            if (!$Post->checkAuthor($updatedPost)) {
                $msg .= PHP_EOL . '  ' . L('UNAUTHORIZED');
                $msg .= PHP_EOL . str_repeat('-', 80);
                \Think\Log::write($msg, 'DEBUG');

                $this->response(L('UNAUTHORIZED'), 'json', 401);
                return;
            }

            if (!$updatedPost) {
                $msg .= PHP_EOL . '  validation error: ' . $Post->getError();

                $data = [
                    'post' => $input,
                    'validationError' => $Post->getError(),
                ];

                $msg .= PHP_EOL . str_repeat('-', 80);
                \Think\Log::write($msg, 'DEBUG');

                $this->response($data, 'json', 400);
            } else {
                $msg .= PHP_EOL . '  validation passed';

                $result = $Post->save();

                $msg .= PHP_EOL . '  $result = ' . print_r($result, true);

                if ($result !== false) {
                    $data = [
                        'post' => $Post->find($id),
                    ];
                    $meta = [
                        'message' => L('SAVE_POST_SUCCESS'),
                    ];

                    $msg .= PHP_EOL . '  $data = ' . print_r($data, true);
                    $msg .= PHP_EOL . '  $meta = ' . print_r($meta, true);
                    $msg .= PHP_EOL . str_repeat('-', 80);
                    \Think\Log::write($msg, 'DEBUG');

                    $this->response(compact('data', 'meta'), 'json', 200);
                } else {
                    $meta = [
                        'message' => L('SAVE_POST_FAILURE'),
                    ];

                    $msg .= PHP_EOL . '  $meta = ' . print_r($meta, true);
                    $msg .= PHP_EOL . str_repeat('-', 80);
                    \Think\Log::write($msg, 'DEBUG');

                    $this->response(compact('meta'), 'json', 500);
                }
            }
        } catch (Exception $e) {
            $msg .= PHP_EOL . 'error: ' . $e->getMessage();
            throw $e;
        } finally {
            $msg .= PHP_EOL . str_repeat('-', 80);
            \Think\Log::write($msg, 'DEBUG');
        }
    }

    /**
     * 删除文章。
     * @param int $id 文章id
     * @return void
     */
    public function delete($id)
    {
        if (!IS_DELETE) {
            return;
        }

        $msg = PHP_EOL . 'Api\Controller\PostController::delete():'
            . PHP_EOL . '  $id = ' . $id;

        try {
            $Post = D('Home/Post');
            $post = $Post->find($id);

            $msg .= PHP_EOL . '  $post = ' . print_r($post, true);

            if (!$post) {
                $msg .= PHP_EOL . '  ' . L('POST_NOT_FOUND');

                $this->response(L('POST_NOT_FOUND'), 'json', 400);
                return;
            }

            if (!$Post->checkAuthor($post)) {
                $msg .= PHP_EOL . '  ' . L('UNAUTHORIZED');
                $msg .= PHP_EOL . str_repeat('-', 80);
                \Think\Log::write($msg, 'DEBUG');

                $this->response(L('UNAUTHORIZED'), 'json', 401);
                return;
            }

            $result = $Post->delete();

            $msg .= PHP_EOL . '  $result = ' . print_r($result, true);

            $code = $result !== false ? 200 : 500;
            $message = $result !== false ?
                L('DELETE_POST_SUCCESS') :
                L('DELETE_POST_FAILURE');
            $meta = compact('message');

            $msg .= PHP_EOL . '  $meta = ' . print_r($meta, true);
            $msg .= PHP_EOL . str_repeat('-', 80);
            \Think\Log::write($msg, 'DEBUG');

            $this->response(compact('meta'), 'json', $code);
        } catch (Exception $e) {
            $msg .= PHP_EOL . 'error: ' . $e->getMessage();
            throw $e;
        } finally {
            $msg .= PHP_EOL . str_repeat('-', 80);
            \Think\Log::write($msg, 'DEBUG');
        }
    }
}
